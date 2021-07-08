<?php

namespace App\Services\DrcrMemo;

use Carbon\Carbon;
use App\Enums\Currencies;
use App\Enums\DrcrStatus;
use App\Models\UserAccount;
use App\Exports\DRCR\DRCRReport;
use App\Enums\TransactionStatuses;
use App\Enums\ReferenceNumberTypes;
use App\Enums\SuccessMessages;
use Maatwebsite\Excel\Facades\Excel;
use App\Enums\TransactionCategoryIds;
use App\Traits\Errors\WithDrcrMemoErrors;
use App\Services\Utilities\PDF\IPDFService;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\LogHistory\LogHistory;

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
    
    
    public function __construct(IDrcrMemoRepository $drcrMemoRepository,
    IReferenceNumberService $referenceNumberService,
    IUserAccountRepository $userAccountRepository,
    IUserBalanceInfoRepository $userBalanceRepository,
    IUserTransactionHistoryRepository $userTransHistory, 
    IPDFService $pdfService, IResponseService $responseService)
    {
        $this->pdfService = $pdfService;
        $this->drcrMemoRepository = $drcrMemoRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->userAccountRepository = $userAccountRepository;
        $this->userBalanceRepository = $userBalanceRepository;
        $this->userTransHistory = $userTransHistory;
        $this->responseService = $responseService;
    }

    public function getList(UserAccount $user, $data, $per_page = 15)
    {
        if ($data === 'ALL') return $this->drcrMemoRepository->getList($user, $per_page);
        if ($data !== DrcrStatus::Approve && $data !== DrcrStatus::Decline && $data !== DrcrStatus::Pending) return $this->invalidStatus();
        return $this->drcrMemoRepository->getListByCreatedBy($user, $data, $per_page);
    }


    public function getAllList(UserAccount $user, $data, $per_page = 15)
    {
        if ($data === 'ALL') return $this->drcrMemoRepository->getAllPaginate($per_page);
        if ($data !== DrcrStatus::Approve && $data !== DrcrStatus::Decline && $data !== DrcrStatus::Pending) return $this->invalidStatus();
        return $this->drcrMemoRepository->getAllList($user, $data, $per_page);
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
        if(!$user) return $this->userAccountNotFound();

        $customerName = $user->userDetail->first_name . ' ' . $user->userDetail->last_name;
        $balance = $user->balanceInfo->available_balance;
        return ['customer_name' => $customerName, 'balance' => $balance];
    }

    public function store(UserAccount $user, $data)
    {
        $customer = $this->getUserByAccountNumber($data);
        $typeOfMemo = $data['typeOfMemo'];

        if(!$customer) return $this->userAccountNotFound();
        if($typeOfMemo !== ReferenceNumberTypes::CR && $typeOfMemo !== ReferenceNumberTypes::DR) return $this->invalidTypeOfMemo();

        if ($data['typeOfMemo'] == ReferenceNumberTypes::DR) {
            $isEnough = $this->checkAmount($data, $customer->id);
            if (!$isEnough) $this->insuficientBalance();
        }
        return $this->drcrMemo($user, $data, $customer->id);
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
        if(empty($remarks) && $status == DrcrStatus::Decline) return $this->isEmpty();
        if ($this->userTransHistory->isExisting($drcrMemo->id)) return $this->isExisting();

        if ($status == DrcrStatus::Approve) {
            if ($drcrMemo->type_of_memo == ReferenceNumberTypes::DR) {
                if (!$isEnough) return $this->insuficientBalance();
                $this->debitMemo($drcrMemo->user_account_id, $drcrMemo->amount);
            }
            if ($drcrMemo->type_of_memo == ReferenceNumberTypes::CR) {
                $this->creditMemo($drcrMemo->user_account_id, $drcrMemo->amount);
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
        $newMemo = [
            'user_account_id' => $customerID,
            'type_of_memo' => $data['typeOfMemo'],
            'reference_number' => $refNo,
            'transaction_category_id' => $tranCatId,
            'amount' => $data['amount'],
            'currency_id' => Currencies::philippinePeso,
            'category' => $data['category'],
            'description' => $data['description'],
            'status' => TransactionStatuses::pending,
            'created_by' => $user->id,
            'user_created' => $user->id,
        ];

        return $this->drcrMemoRepository->create($newMemo);
    }

    public function report(array $params) {
        $data = $this->drcrMemoRepository->reportData($params['from'], $params['to']);
        $file_name = $params['from'] . "-" . $params['to'] . "." . $params['type'];
        if($params['type'] == 'PDF') {
            $data = $this->processData($data);
            $records = [
                'records' => $data
            ];
            ini_set("pcre.backtrack_limit", "5000000");
            return $this->pdfService->generatePDFNoUserPassword($records, 'reports.log_histories.log_histories');
        } 
        else if($params['type'] == 'CSV') {
            return Excel::download(new DRCRReport($data, $params['from'], $params['to'], $params), $file_name, \Maatwebsite\Excel\Excel::CSV);
        } 
        else if($params['type'] == 'API')  {
            return $this->responseService->successResponse($data->toArray(), SuccessMessages::success);
        }
        else {
            return Excel::download(new DRCRReport($data, $params['from'], $params['to'], $params), $file_name, \Maatwebsite\Excel\Excel::XLSX);
        }
    }
}
