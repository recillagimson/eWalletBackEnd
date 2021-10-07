<?php

namespace App\Services\AddMoney\UBP;

use App\Enums\ReferenceNumberTypes;
use App\Enums\SquidPayModuleTypes;
use App\Enums\TransactionCategories;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Enums\UbpResponseCodes;
use App\Models\InAddMoneyUbp;
use App\Models\UserAccount;
use App\Models\UserBalanceInfo;
use App\Repositories\InAddMoneyUbp\IInAddMoneyUbpRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UBPAccountToken\IUBPAccountTokenRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\UBP\IUbpAccountService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Errors\WithUbpErrors;
use App\Traits\Errors\WithUserErrors;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UbpAddMoneyService implements IUbpAddMoneyService
{
    use WithAuthErrors, WithUserErrors, WithTransactionErrors, WithUbpErrors;

    private IUBPService $ubpService;
    private IUBPAccountTokenRepository $ubpAccountTokens;
    private IUbpAccountService $ubpAccountService;
    private IUserAccountRepository $userAccounts;
    private ITransactionValidationService $transactionValidationService;
    private IServiceFeeRepository $serviceFees;
    private IInAddMoneyUbpRepository $addMoneyUbps;
    private IReferenceNumberService $refNoService;
    private IUserTransactionHistoryRepository $transactionHistories;
    private ILogHistoryService $logHistories;

    public function __construct(IUBPService                       $ubpService,
                                IUbpAccountService                $ubpAccountService,
                                ITransactionValidationService     $transactionValidationService,
                                IReferenceNumberService           $refNoService,
                                ILogHistoryService                $logHistories,
                                IUBPAccountTokenRepository        $ubpAccountTokens,
                                IUserAccountRepository            $userAccounts,
                                IServiceFeeRepository             $serviceFees,
                                IInAddMoneyUbpRepository          $addMoneyUbps,
                                IUserTransactionHistoryRepository $transactionHistories)
    {
        $this->ubpService = $ubpService;
        $this->ubpAccountService = $ubpAccountService;
        $this->transactionValidationService = $transactionValidationService;
        $this->refNoService = $refNoService;

        $this->ubpAccountTokens = $ubpAccountTokens;
        $this->userAccounts = $userAccounts;
        $this->serviceFees = $serviceFees;
        $this->addMoneyUbps = $addMoneyUbps;
        $this->transactionHistories = $transactionHistories;
        $this->logHistories = $logHistories;
    }

    public function addMoney(string $userId, float $amount): InAddMoneyUbp
    {
        try {
            DB::beginTransaction();

            $user = $this->userAccounts->getUser($userId);
            if (!$user) $this->userAccountNotFound();

            Log::info('UBP Add Money User', $user->toArray());
            $this->transactionValidationService->validateUser($user);

            $serviceFee = $this->serviceFees
                ->getByTierAndTransCategory($user->tier_id, TransactionCategoryIds::cashinUBP);

            $token = $this->ubpAccountService->checkAccountLink($userId);
            if (!$token) $this->ubpNoAccountLinked();

            $serviceFeeId = $serviceFee ? $serviceFee->id : '';
            $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
            $totalAmount = $amount + $serviceFeeAmount;

            $this->transactionValidationService->validate($user,
                TransactionCategoryIds::cashinUBP, $totalAmount);

            $refNo = $this->refNoService->generate(ReferenceNumberTypes::AddMoneyViaWebBank);
            $currentDate = Carbon::now();
            $transactionDate = $currentDate->toDateTimeLocalString('millisecond');
            $userFullName = $user->profile->last_name . ', ' . $user->profile->first_name;

            $addMoney = $this->createTransaction($userId, $refNo, $amount, $serviceFeeAmount, $serviceFeeId, $currentDate);
            if (!$addMoney) $this->transactionFailed();

            $payment = [
                'user_account_id' => $userId,
                'user_full_name' => $userFullName,
                'reference_number' => $refNo,
                'transaction_date' => $transactionDate,
                'amount' => $amount
            ];

            $paymentResponse = $this->ubpService->merchantPayment($token->access_token, $payment);
            $data = $paymentResponse->json();

            if (!$paymentResponse->successful()) {
                Log::error('Add Money via UBP Error: ', $data);
                $this->transactionFailed();
            }

            $payload = $data['payload'];
            $addMoney = $this->updateTransaction($addMoney, $payload);
            $userBalance = $this->updateUserBalance($user->balanceInfo, $addMoney);

            $this->updateUserHistory($addMoney);
            $this->updateLogs($addMoney);

//            $this->sendNotification($user, $addMoney);
            DB::commit();
            return $addMoney;
        } catch (Exception $ex) {
            DB::rollBack();

            Log::error('Add Money via UBP Error', $ex->getTrace());
            throw $ex;
        }
    }

    public function createTransaction(string $userId, string $refNo, float $amount, float $serviceFee,
                                      string $serviceFeeId, Carbon $transDate): InAddMoneyUbp
    {
        $data = [
            'user_account_id' => $userId,
            'reference_number' => $refNo,
            'amount' => $amount,
            'service_fee' => $serviceFee,
            'service_fee_id' => $serviceFeeId,
            'total_amount' => $amount + $serviceFee,
            'status' => TransactionStatuses::pending,
            'transaction_date' => $transDate,
            'user_created' => $userId
        ];

        return $this->addMoneyUbps->create($data);
    }

    private function updateTransaction(InAddMoneyUbp $addMoney, array $payload): InAddMoneyUbp
    {
        $code = $payload['code'];

        if ($code === UbpResponseCodes::receivedRequest || $code === UbpResponseCodes::forConfirmation
            || $code === UbpResponseCodes::processing) {
            $status = TransactionStatuses::pending;
        } elseif ($code === UbpResponseCodes::successfulTransaction) {
            $status = TransactionStatuses::success;
        } else {
            $status = TransactionStatuses::failed;
        }

        $addMoney->status = $status;
        $addMoney->provider_reference_number = $payload['ubpTranId'];
        $addMoney->user_updated = $addMoney->user_account_id;
        $addMoney->transaction_response = json_encode($payload);
        $addMoney->save();

        return $addMoney;
    }

    private function updateUserBalance(UserBalanceInfo $userBalance, InAddMoneyUbp $addMoney): UserBalanceInfo
    {
        if ($addMoney->status === TransactionStatuses::success) {
            $userBalance->available_balance += $addMoney->amount;
            $userBalance->save();
        }

        return $userBalance;
    }

    private function updateUserHistory(InAddMoneyUbp $addMoney)
    {
        if ($addMoney->status === TransactionStatuses::success) {
            $this->transactionHistories->log($addMoney->user_account_id, TransactionCategoryIds::cashinUBP,
                $addMoney->id, $addMoney->reference_number, $addMoney->total_amount, $addMoney->transaction_date,
                $addMoney->user_account_id);
        }
    }

    private function updateLogs(InAddMoneyUbp $addMoney)
    {
        if ($addMoney->status === TransactionStatuses::pending)
            $remarks = "Pending cash-in via UBP with amount: $addMoney->amount.";
        elseif ($addMoney->status === TransactionStatuses::success)
            $remarks = "Successful Cash-in via UBP with amount: $addMoney->amount.";
        else
            $remarks = "Failed Cash-in via UBP with amount: $addMoney->amount.";

        $this->logHistories->logUserHistory($addMoney->user_account_id, $addMoney->reference_number, SquidPayModuleTypes::AddMoneyViaUBP,
            null, $addMoney->transaction_date, $remarks, TransactionCategories::AddMoneyUBP);
    }

    public function processPending(string $userId): array
    {
        try {
            DB::beginTransaction();

            $user = $this->userAccounts->getUser($userId);
            $pendingAddMoneys = $this->addMoneyUbps->getPending($userId);
            $userBalance = $user->balanceInfo;
            $successCount = 0;
            $failCount = 0;

            foreach ($pendingAddMoneys as $addMoney) {
                $response = $this->ubpService->checkMerchantPaymentStatus($addMoney->reference_number);
                $data = $response->json();

                if (!$response->successful()) {
                    Log::error('UBP Add Money Check Status Error:', $data);
                    continue;
                }

                $record = $data['payload']['record'];
                $addMoney = $this->updateTransaction($addMoney, $record);
                $userBalance = $this->updateUserBalance($userBalance, $addMoney);

                if ($addMoney->status === TransactionStatuses::success) $successCount++;
                if ($addMoney->status === TransactionStatuses::failed) $failCount++;
            }

            DB::commit();

            return [
                'total_pending_count' => $pendingAddMoneys->count(),
                'success_count' => $successCount,
                'failed_count' => $failCount
            ];
        } catch (Exception $ex) {
            DB::rollBack();

            Log::error('Add Money UBP Process Pending Error:', [
                'userId' => $userId,
                'error' => $ex->getTraceAsString()
            ]);
        }
    }

    private function sendNotification(UserAccount $user, InAddMoneyUbp $addMoney)
    {
        if ($addMoney->status === TransactionStatuses::success) {

        }
    }


}
