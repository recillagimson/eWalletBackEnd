<?php

namespace App\Services\DrcrMemo;

use Carbon\Carbon;
use App\Enums\Currencies;
use App\Enums\DrcrStatus;
use App\Models\UserAccount;
use App\Enums\SuccessMessages;
use App\Exports\DRCR\DRCRReport;
use App\Enums\TransactionStatuses;
use App\Enums\ReferenceNumberTypes;
use Maatwebsite\Excel\Facades\Excel;
use App\Enums\TransactionCategoryIds;
use App\Traits\LogHistory\LogHistory;
use Illuminate\Support\Facades\Storage;
use App\Traits\Errors\WithDrcrMemoErrors;
use App\Exports\DRCR\DRCRReportSupervisor;
use App\Exports\DRCR\DRCRWithBalanceReport;
use App\Services\Utilities\PDF\IPDFService;
use Illuminate\Validation\ValidationException;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Services\Utilities\Responses\IResponseService;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Imports\DrcrMemo\DrcrMemoImport;
use App\Exports\DRCR\DRCRBulkErrorList;
use App\Repositories\DrcrMemoControlNumber\DrcrMemoControlNumberRepository;
use App\Enums\Roles;
use App\Repositories\UserUtilities\UserRole\UserRoleRepository;

class DrcrMemoService implements IDrcrMemoService
{
    use WithDrcrMemoErrors, LogHistory;

    private IDrcrMemoRepository $drcrMemoRepository;
    private IReferenceNumberService $referenceNumberService;
    private IUserAccountRepository $userAccountRepository;
    private IUserBalanceInfoRepository $userBalanceRepository;
    private IUserTransactionHistoryRepository $userTransHistory;
    private IPDFService $pdfService;
    private IResponseService $responseService;
    private DrcrMemoControlNumberRepository $drcrMemoControlNumberRepository;
    private UserRoleRepository $userRoleRepository;


    public function __construct(
        IDrcrMemoRepository $drcrMemoRepository,
        IReferenceNumberService $referenceNumberService,
        IUserAccountRepository $userAccountRepository,
        IUserBalanceInfoRepository $userBalanceRepository,
        IUserTransactionHistoryRepository $userTransHistory,
        IPDFService $pdfService,
        IResponseService $responseService,
        DrcrMemoControlNumberRepository $drcrMemoControlNumberRepository,
        UserRoleRepository $userRoleRepository
    ) {
        $this->pdfService = $pdfService;
        $this->drcrMemoRepository = $drcrMemoRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->userAccountRepository = $userAccountRepository;
        $this->userBalanceRepository = $userBalanceRepository;
        $this->userTransHistory = $userTransHistory;
        $this->responseService = $responseService;
        $this->drcrMemoControlNumberRepository = $drcrMemoControlNumberRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function getList(UserAccount $user, $data, $per_page = 15, $from = '', $to = '')
    {
        if ($data === 'ALL') return $this->drcrMemoRepository->getList($user, $per_page, $from, $to);
        if ($data !== DrcrStatus::Approve && $data !== DrcrStatus::Decline && $data !== DrcrStatus::Pending) return $this->invalidStatus();
        return $this->drcrMemoRepository->getListByCreatedBy($user, $data, $per_page, $from, $to);
    }


    public function getAllList(UserAccount $user, $data, $per_page = 15, $from = '', $to = '')
    {
        if ($data === 'ALL') return $this->drcrMemoRepository->getAllPaginate($per_page, $from, $to);
        if ($data !== DrcrStatus::Approve && $data !== DrcrStatus::Decline && $data !== DrcrStatus::Pending) return $this->invalidStatus();
        return $this->drcrMemoRepository->getAllList($user, $data, $per_page, $from, $to);
    }


    public function show(string $referenceNumber): array
    {
        $show = $this->drcrMemoRepository->getByReferenceNumber($referenceNumber);
        if (!$show) return $this->referenceNumberNotFound();
        return [$show];
    }


    public function getUser(string $accountNumber): array
    {
        $user = $this->userAccountRepository->getUserByAccountNumber($accountNumber);
        if (!$user) return $this->userAccountNotFound();

        $customerName = $user->userDetail->first_name . ' ' . $user->userDetail->last_name;
        $balance = $user->balanceInfo->available_balance;
        return ['customer_name' => $customerName, 'balance' => $balance];
    }

    public function store(UserAccount $user, $data)
    {
        $customer = $this->getUserByAccountNumber($data);
        $typeOfMemo = $data['typeOfMemo'];

        if (!$customer) return $this->userAccountNotFound();
        if ($typeOfMemo !== ReferenceNumberTypes::CR && $typeOfMemo !== ReferenceNumberTypes::DR) return $this->invalidTypeOfMemo();

        if ($data['typeOfMemo'] == ReferenceNumberTypes::DR) {
            $isEnough = $this->checkAmount($data, $customer->id);
            if (!$isEnough) $this->insuficientBalance();
            // Trigger available to pending
            $wallet = $this->userBalanceRepository->getByUserAccountID($customer->id);
            if ($wallet) {
                // deduct available balance
                $wallet->available_balance = (float) $wallet->available_balance -  (float) $data['amount'];
                // add pending balance
                $wallet->pending_balance = (float) $wallet->pending_balance +  (float) $data['amount'];
                $wallet->save();
            } else {
                throw ValidationException::withMessages([
                    'wallet_not_found', 'Wallet not found'
                ]);
            }
        }
        $drcrMemo = $this->drcrMemo($user, $data, $customer->id);

        if ($drcrMemo->status == DrcrStatus::Approve) {
            $approveData = [
                "referenceNumber" => $drcrMemo->reference_number,
                "status" => "A",
                "remarks" => "The Creator is a Treasury Manager.",
            ];
            $this->approval($user, $approveData);
        }

        return $drcrMemo;
    }


    public function updateMemo(UserAccount $user, $data)
    {
        $memo = $this->drcrMemoRepository->getByReferenceNumber($data['referenceNumber']);
        $customer = $this->userAccountRepository->get($memo->user_account_id);
        if ($data['typeOfMemo'] == ReferenceNumberTypes::DR) {
            $isEnough = $this->checkAmount($data, $customer->id);
            if (!$isEnough) $this->insuficientBalance();
        }
        return $this->drcrMemoRepository->updateMemo($user, $data);
    }


    public function approval(UserAccount $user, $data): array
    {
        $drcrMemo = $this->drcrMemoRepository->getByReferenceNumber($data['referenceNumber']);
        if (!$drcrMemo) return $this->referenceNumberNotFound();

        $data['amount'] = $drcrMemo->amount;
        $status = $data['status'];
        $remarks = $data['remarks'];
        $isEnough = $this->checkAmount($data, $drcrMemo->user_account_id);

        if ($status !== DrcrStatus::Approve && $status !== DrcrStatus::Decline && $status !== DrcrStatus::Pending) return $this->invalidStatus1();
        if (empty($remarks) && $status == DrcrStatus::Decline) return $this->isEmpty();
        if ($this->userTransHistory->isExisting($drcrMemo->id)) return $this->isExisting();

        if ($status == DrcrStatus::Approve) {
            if ($drcrMemo->type_of_memo == ReferenceNumberTypes::DR) {
                if (!$isEnough) return $this->insuficientBalance();
                $this->debitMemo($drcrMemo->user_account_id, $drcrMemo->amount);
                // Trigger available to pending
                $wallet = $this->userBalanceRepository->getByUserAccountID($drcrMemo->user_account_id);
                if ($wallet) {
                    // deduct pending balance
                    $wallet->pending_balance = (float) $wallet->pending_balance -  (float) $drcrMemo->amount;
                    $wallet->save();
                } else {
                    throw ValidationException::withMessages([
                        'wallet_not_found', 'Wallet not found'
                    ]);
                }
            }
            if ($drcrMemo->type_of_memo == ReferenceNumberTypes::CR) {
                $this->creditMemo($drcrMemo->user_account_id, $drcrMemo->amount);
            }
        } else if ($status == DrcrStatus::Decline) {
            if ($drcrMemo->type_of_memo == ReferenceNumberTypes::DR) {
                // Trigger pending to available
                $wallet = $this->userBalanceRepository->getByUserAccountID($drcrMemo->user_account_id);
                if ($wallet) {
                    // deduct pending balance
                    $wallet->pending_balance = (float) $wallet->pending_balance -  (float) $drcrMemo->amount;
                    // Add to available
                    $wallet->available_balance = (float) $wallet->available_balance +  (float) $drcrMemo->amount;
                    $wallet->save();
                } else {
                    throw ValidationException::withMessages([
                        'wallet_not_found', 'Wallet not found'
                    ]);
                }
            }
        }

        $drcr = $this->drcrMemoRepository->updateDrcr($user, $data);
        $drcr1 = (object) $this->drcrMemoRepository->updateDrcr($user, $data);

        $this->userTransHistory->log($drcr1->user_account_id, $drcr1->transaction_category_id, $drcr1->id, $drcr1->reference_number, $drcr1->amount, Carbon::parse($drcr1->updated_at), $drcr1->user_created);

        return $drcr;
    }


    // PRIVATE METHODS

    private function debitMemo($userID, $amount)
    {
        $balance = $this->userBalanceRepository->getUserBalance($userID);
        $balance -= $amount;
        $this->userBalanceRepository->updateUserBalance($userID, $balance);
    }

    private function creditMemo($userID, $amount)
    {
        $balance = $this->userBalanceRepository->getUserBalance($userID);
        $balance += $amount;
        $this->userBalanceRepository->updateUserBalance($userID, $balance);
    }

    private function checkAmount($data, $customerID): bool
    {
        $balance = $this->userBalanceRepository->getUserBalance($customerID);
        if ($balance >= $data['amount']) return true;
        return false;
    }

    private function setTypeOfMemo(string $typeOfMemo): string
    {
        if ($typeOfMemo === ReferenceNumberTypes::DR) return ReferenceNumberTypes::DR;
        return ReferenceNumberTypes::CR;
    }

    private function setTransactionCategory(string $typeOfMemo): string
    {
        if ($typeOfMemo === ReferenceNumberTypes::DR) return TransactionCategoryIds::drMemo;
        return TransactionCategoryIds::crMemo;
    }

    private function getReference($data): string
    {
        return $this->referenceNumberService->generate($this->setTypeOfMemo($data));
    }

    private function getUserByAccountNumber($data)
    {
        return $this->userAccountRepository->getUserByAccountNumber($data['accountNumber']);
    }

    private function drcrMemo(UserAccount $user, $data, string $customerID)
    {
        $memoType = $this->setTypeOfMemo($data['typeOfMemo']);
        $refNo = $this->referenceNumberService->generate($memoType);
        $tranCatId = $this->setTransactionCategory($data['typeOfMemo']);
        $isManager = $this->userRoleRepository->hasUserRole($user->id, Roles::treasuryManager);
        $newMemo = [
            'user_account_id' => $customerID,
            'type_of_memo' => $data['typeOfMemo'],
            'reference_number' => $refNo,
            'transaction_category_id' => $tranCatId,
            'amount' => $data['amount'],
            'currency_id' => Currencies::philippinePeso,
            'category' => $data['category'],
            'description' => $data['description'],
            'status' => $isManager ? TransactionStatuses::success : TransactionStatuses::pending,
            'created_by' => $user->id,
            'user_created' => $user->id,
        ];

        return $this->drcrMemoRepository->create($newMemo);
    }

    public function report(array $params, string $currentUser = '')
    {

        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'XLSX';
        $filter_by = '';
        $filter_value = '';

        if ($params && isset($params['from']) && isset($params['to'])) {
            $from = $params['from'];
            $to = $params['to'];
        }

        if ($params && isset($params['type'])) {
            $type = $params['type'];
        }

        if ($params && isset($params['filter_by']) && isset($params['filter_value'])) {
            $filter_by = $params['filter_by'];
            $filter_value = $params['filter_value'];
        }

        $data = $this->drcrMemoRepository->reportData($from, $to, $filter_by, $filter_value);
        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        if ($params['type'] == 'PDF') {
            Excel::store(new DRCRReport($data, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::MPDF);
            $temp_url = $this->s3TempUrl($fileName);
            // $data = $this->processData($data);
            // $records = [
            //     'records' => $data
            // ];
            // ini_set("pcre.backtrack_limit", "5000000");
            // $file = $this->pdfService->generatePDFNoUserPassword($records, 'reports.log_histories.log_histories', true);
            // $url = $this->storeToS3($currentUser, $file['file_name'], $fileName);
            // unlink($file['file_name']);
            // $temp_url = $this->s3TempUrl($url);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } else if ($params['type'] == 'CSV') {
            Excel::store(new DRCRReport($data, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } else if ($params['type'] == 'API') {
            $processed = $this->processData($data, true);
            return $this->responseService->successResponse($processed, SuccessMessages::success);
        } else {
            Excel::store(new DRCRReport($data, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        }
    }

    public function storeToS3(string $currentUser, $file, string $fileName)
    {
        $folderName = 'reports/' . $currentUser;
        $generated_link = Storage::disk('s3')->putFileAs($folderName, $file, $fileName);
        return $generated_link;
    }

    public function s3TempUrl(string $generated_link)
    {
        $temp_url = Storage::disk('s3')->temporaryUrl($generated_link, Carbon::now()->addMinutes(30));
        return $temp_url;
    }

    public function reportFiltered(array $attr)
    {
        $from = Carbon::now()->subDays(30)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');
        $type = 'API';
        $filterBy = '';
        $filterValue = '';
        $userId = 0;
        $isPending = false;

        if ($attr && isset($attr['from']) && isset($attr['to'])) {
            $from = $attr['from'];
            $to = $attr['to'];
        }

        if ($attr && isset($attr['type'])) {
            $type = $attr['type'];
        }

        if ($attr && isset($attr['userId'])) {
            $userId = $attr['userId'];
        }

        if ($attr && isset($attr['filter_by'])) {
            $filterBy = $attr['filter_by'];
        }

        if ($attr && isset($attr['filter_value'])) {
            $filterValue = $attr['filter_value'];
        }

        if ($attr && isset($attr['is_pending_only'])) {
            $isPending = $attr['is_pending_only'];
        }

        $data = [];
        $fileName = 'reports/' . $from . "-" . $to . "." . $type;

        if ($type == 'API') {
            $data = $this->drcrMemoRepository->reportPerUserSupervisor($from, $to, $filterBy, $filterValue, $userId, true, $isPending);
        } else {
            $data = $this->drcrMemoRepository->reportPerUserSupervisor($from, $to, $filterBy, $filterValue, $userId, false, $isPending);
        }

        if ($type == 'API') {
            return $this->responseService->successResponse($data->toArray(), SuccessMessages::success);
        }
        // CSV
        else if ($type == 'CSV') {
            Excel::store(new DRCRReportSupervisor($data, $from, $to, $type), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        }
        // XLSX
        else if ($type == 'XLSX') {
            Excel::store(new DRCRReportSupervisor($data, $from, $to, $type), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        }
    }

    // DRCR MEMO PER USER
    // DRCR MEMO PER USER
    // DRCR MEMO PER USER

    public function reportFilteredPerUser(array $attr, $isPerUserStatus)
    {
        $from = Carbon::now()->subDays(30)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');
        $type = 'API';
        $filterBy = '';
        $filterValue = '';
        $userId = 0;
        $isPerUser = false;

        if ($attr && isset($attr['from']) && isset($attr['to'])) {
            $from = $attr['from'];
            $to = $attr['to'];
        }

        if ($attr && isset($attr['type'])) {
            $type = $attr['type'];
        }

        if ($attr && isset($attr['userId'])) {
            $userId = $attr['userId'];
        }

        if ($attr && isset($attr['filter_by'])) {
            $filterBy = $attr['filter_by'];
        }

        if ($attr && isset($attr['filter_value'])) {
            $filterValue = $attr['filter_value'];
        }

        if ($isPerUserStatus) {
            $isPerUser = true;
        }

        $data = [];
        $fileName = 'reports/' . $from . "-" . $to . "." . $type;

        if ($type == 'API') {
            $data = $this->drcrMemoRepository->updatedReportPerUserSupervisor($from, $to, $filterBy, $filterValue, $userId, true, $isPerUser);
        } else {
            $data = $this->drcrMemoRepository->updatedReportPerUserSupervisor($from, $to, $filterBy, $filterValue, $userId, false, $isPerUser);
        }

        if ($type == 'API') {
            return $this->responseService->successResponse($data->toArray(), SuccessMessages::success);
        }
        // CSV
        else if ($type == 'CSV') {
            Excel::store(new DRCRWithBalanceReport($data, $from, $to, $type), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        }
        // XLSX
        else if ($type == 'XLSX') {
            Excel::store(new DRCRWithBalanceReport($data, $from, $to, $type), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        }
    }

    public function batchUpload(UserAccount $user, $file)
    {
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $exists = $this->drcrMemoControlNumberRepository->findByControlNumber($filename);

        if ($exists && $exists->status == 'Success') return $this->controlNumberAlreadyUploaded();

        if ($exists) {
            $this->drcrMemoControlNumberRepository->update($exists, [
                'control_number' => $filename,
                'user_updated' => $user->id,
                'status' => 'Pending',

            ]);
            $controlNumber = $exists;
        } else {
            $controlNumber = $this->drcrMemoControlNumberRepository->create([
                'control_number' => $filename,
                'user_created' => $user->id,
                'user_updated' => $user->id,
                'status' => 'Pending',

            ]);
        }

        try {
            $import = new DrcrMemoImport(
                $user,
                $this->referenceNumberService,
                $this->drcrMemoRepository,
                $this->userAccountRepository,
                $this->userBalanceRepository,
                $this->userTransHistory,
                $controlNumber->id
            );
            Excel::import($import, $file);

            $this->drcrMemoControlNumberRepository->update($controlNumber, [
                'user_updated' => $user->id,
                'status' => 'Success',

            ]);

            return;
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $exportName = $filename . "- Errors.xlsx";

            $failData = collect();
            foreach ($failures as $key => $fail) {
                $failData->push($fail->values() + ['remarks' => $fail->errors()]);
            }

            Excel::store(new DRCRBulkErrorList($failData), $exportName, 's3');

            $temp_url = $this->s3TempUrl($exportName);

            throw ValidationException::withMessages([
                'temp_url' => $temp_url,
                'data' => $failData
            ]);
        }
    }
}
