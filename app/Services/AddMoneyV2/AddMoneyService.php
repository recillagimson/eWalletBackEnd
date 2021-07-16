<?php


namespace App\Services\AddMoneyV2;


use App\Enums\DragonPayStatusTypes;
use App\Enums\ReferenceNumberTypes;
use App\Enums\SquidPayModuleTypes;
use App\Enums\TransactionCategories;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Models\InAddMoneyFromBank;
use App\Models\UserBalanceInfo;
use App\Repositories\InAddMoney\IInAddMoneyRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\ThirdParty\DragonPay\IDragonPayService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Errors\WithUserErrors;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddMoneyService implements IAddMoneyService
{
    use WithUserErrors, WithTransactionErrors;

    private IUserAccountRepository $userAccounts;
    private ITransactionValidationService $transactionValidationService;
    private IDragonPayService $dragonPayService;
    private IReferenceNumberService $referenceNumberService;
    private IInAddMoneyRepository $addMoney;
    private ILogHistoryService $logHistoryService;
    private IServiceFeeRepository $serviceFees;
    private IUserTransactionHistoryRepository $userTransactionHistories;

    public function __construct(IUserAccountRepository $userAccounts,
                                IInAddMoneyRepository $addMoney,
                                IServiceFeeRepository $serviceFees,
                                IUserTransactionHistoryRepository $userTransactionHistories,
                                ITransactionValidationService $transactionValidationService,
                                IDragonPayService $dragonPayService,
                                IReferenceNumberService $referenceNumberService,
                                ILogHistoryService $logHistoryService)
    {
        $this->userAccounts = $userAccounts;
        $this->addMoney = $addMoney;
        $this->serviceFees = $serviceFees;
        $this->userTransactionHistories = $userTransactionHistories;

        $this->transactionValidationService = $transactionValidationService;
        $this->dragonPayService = $dragonPayService;
        $this->referenceNumberService = $referenceNumberService;
        $this->logHistoryService = $logHistoryService;
    }

    public function generateUrl(string $userId, array $data): array
    {
        $user = $this->userAccounts->getUser($userId);
        if (!$user) $this->userAccountNotFound();
        $this->transactionValidationService->validateUser($user);

        $serviceFee = $this->serviceFees->getByTierAndTransCategory($user->tier_id,
            TransactionCategoryIds::cashinDragonPay);
        $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
        $serviceFeeId = $serviceFee ? $serviceFee->id : '';
        $totalAmount = $data['amount'] + $serviceFeeAmount;

        $this->transactionValidationService->validate($user,
            TransactionCategoryIds::cashinDragonPay, $totalAmount);

        $fullName = $user->profile->full_name;
        $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::AddMoneyViaWebBank);
        $currentDate = Carbon::now();

        $response = $this->dragonPayService->generateUrl($refNo, $user->email, $fullName, $data['amount']);

        if (!$response->successful()) {
            $errors = $response->json();
            Log::error('DragonPay Error', $errors);
            $this->transactionFailed();
        } else {
            $responseData = $response->json();

            if ($responseData['Status'] === DragonPayStatusTypes::requestSuccessful) {
                $this->createTransaction($userId, $refNo, $data['amount'], $serviceFeeAmount,
                    $serviceFeeId, $totalAmount, TransactionCategoryIds::cashinDragonPay, '',
                    $currentDate);

                $this->logHistoryService->logUserHistory($userId,
                    $refNo,
                    SquidPayModuleTypes::AddMoneyViaWebBanksDragonPay,
                    __NAMESPACE__,
                    $currentDate,
                    'Requested to generate DragonPay URL to add money.',
                    TransactionCategories::AddMoneyWebBankDragonPay);

                return $responseData;
            }

            Log::error('DragonPay Error', $responseData);
            $this->transactionFailed();
        }
    }

    public function handlePostBack(array $data)
    {
        $addMoney = $this->addMoney->getByReferenceNumber($data['txnid']);

        if (!$addMoney) return;
        if ($addMoney->status === TransactionStatuses::success) return;
        if ($addMoney->status === TransactionStatuses::failed) return;

        $this->updateStatus($addMoney);;
    }

    public function processPending(string $userId)
    {
        $pendingTransactions = $this->addMoney->getUserPending($userId);
        if (!$pendingTransactions) return;
        if ($pendingTransactions->count() <= 0) return;

        $pendingCount = $pendingTransactions->count();
        $successCount = 0;
        $failedCount = 0;

        foreach ($pendingTransactions as $pendingTransaction) {
            $addMoney = $this->updateStatus($pendingTransaction);

            if ($addMoney->status === TransactionStatuses::success) $successCount += 1;
            if ($addMoney->status === TransactionStatuses::failed) $failedCount += 1;
        }

        return [
            'total_pending_count' => $pendingCount,
            'success_count' => $successCount,
            'failed_count' => $failedCount
        ];
    }

    private function updateStatus(InAddMoneyFromBank $addMoney): InAddMoneyFromBank
    {
        $response = $this->dragonPayService->checkStatus($addMoney->reference_number);
        if (!$response->successful()) {
            $errors = $response->json();

            if ($response->status() === Response::HTTP_NOT_FOUND)
                $errors = ['message' => 'Record not found.'];

            Log::error('DragonPay Check Status Error: ', $errors);
        } else {
            $responseData = $response->json();
            $updatedStatus = $responseData['Status'];

            if ($updatedStatus === DragonPayStatusTypes::Pending) return $addMoney;

            if ($updatedStatus === DragonPayStatusTypes::Success) {
                $user = $this->userAccounts->getUser($addMoney->user_account_id);
                return $this->handleSuccessTransaction($user->balanceInfo, $addMoney, $responseData);
            } else {
                return $this->handleFailedTransaction($addMoney, $responseData);
            }
        }

        return $addMoney;
    }

    private function handleSuccessTransaction(UserBalanceInfo $balanceInfo, InAddMoneyFromBank $addMoney, array $responseData): InAddMoneyFromBank
    {
        try {
            DB::beginTransaction();

            $addMoney->status = TransactionStatuses::success;
            $addMoney->dragonpay_reference = $responseData['RefNo'];
            $addMoney->dragonpay_channel_reference_number = $responseData['ProcMsg'];
            $addMoney->transaction_remarks = $responseData['Description'];
            $addMoney->save();

            $balanceInfo->available_balance += $addMoney->amount;
            $balanceInfo->save();

            $this->userTransactionHistories->log($addMoney->user_account_id,
                TransactionCategoryIds::cashinDragonPay,
                $addMoney->id,
                $addMoney->reference_number,
                $addMoney->amount,
                $addMoney->transaction_date,
                $addMoney->user_account_id);

            $this->logHistoryService->logUserHistory($addMoney->user_account_id,
                $addMoney->reference_number,
                SquidPayModuleTypes::AddMoneyViaWebBanksDragonPay,
                __NAMESPACE__,
                Carbon::now(),
                'Successfully Added Money On The Account via DragonPay',
                TransactionCategories::AddMoneyWebBankDragonPay);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Add Money Success Error: ', $e->getTrace());
        }

        return $addMoney;
    }

    private function handleFailedTransaction(InAddMoneyFromBank $addMoney, array $responseData): InAddMoneyFromBank
    {
        try {
            DB::beginTransaction();

            $addMoney->status = TransactionStatuses::failed;
            $addMoney->dragonpay_reference = $responseData['RefNo'];
            $addMoney->dragonpay_channel_reference_number = $responseData['ProcMsg'];
            $addMoney->transaction_remarks = $responseData['Description'];
            $addMoney->save();

            $this->logHistoryService->logUserHistory($addMoney->user_account_id,
                $addMoney->reference_number,
                SquidPayModuleTypes::AddMoneyViaWebBanksDragonPay,
                __NAMESPACE__,
                Carbon::now(),
                'Failed to add money on the account via DragonPay',
                TransactionCategories::AddMoneyWebBankDragonPay);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Add Money Failed Error: ', $e->getTrace());
        }

        return $addMoney;
    }

    private function createTransaction(string $userAccountID, string $refNo, float $amount, float $serviceFee,
                                       string $serviceFeeID, float $totalAmount, string $transactionCategoryID,
                                       string $transactionRemarks, Carbon $transactionDate)
    {
        $row = [
            'user_account_id' => $userAccountID,
            'reference_number' => $refNo,
            'amount' => $amount,
            'service_fee' => $serviceFee,
            'service_fee_id' => $serviceFeeID,
            'total_amount' => $totalAmount,
            'dragonpay_reference' => null,
            'transaction_date' => $transactionDate,
            'transaction_category_id' => $transactionCategoryID,
            'transaction_remarks' => $transactionRemarks,
            'user_created' => $userAccountID,
            'status' => TransactionStatuses::pending,
        ];

        return $this->addMoney->create($row);
    }


}
