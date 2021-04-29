<?php


namespace App\Services\Send2Bank;


use App\Enums\ReferenceNumberTypes;
use App\Enums\TpaProviders;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Enums\UbpResponseCodes;
use App\Models\OutSend2Bank;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Errors\WithUserErrors;
use App\Traits\UserHelpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;

class Send2BankPesonetService implements ISend2BankService
{
    use WithAuthErrors, WithUserErrors, WithTpaErrors, WithTransactionErrors;
    use UserHelpers;

    private IReferenceNumberService $referenceNumberService;

    private IUserAccountRepository $users;
    private IUserBalanceInfoRepository $userBalances;
    private IServiceFeeRepository $serviceFees;
    private IOutSend2BankRepository $send2banks;
    private IUserTransactionHistoryRepository $transactionHistories;
    private ITransactionValidationService $transactionValidationService;

    public function __construct(IUBPService $ubpService, IReferenceNumberService $referenceNumberService,
                                ITransactionValidationService $transactionValidationService,
                                IUserAccountRepository $users, IUserBalanceInfoRepository $userBalances,
                                IOutSend2BankRepository $send2banks, IServiceFeeRepository $serviceFees,
                                IUserTransactionHistoryRepository $transactionHistories)
    {
        $this->ubpService = $ubpService;
        $this->referenceNumberService = $referenceNumberService;
        $this->transactionValidationService = $transactionValidationService;

        $this->users = $users;
        $this->userBalances = $userBalances;
        $this->serviceFees = $serviceFees;
        $this->send2banks = $send2banks;
        $this->transactionHistories = $transactionHistories;
    }


    public function getBanks(): array
    {
        $response = $this->ubpService->getBanks(TpaProviders::ubpPesonet);
        if (!$response->successful()) $this->tpaErrorOccured('UBP - Pesonet');
        return json_decode($response->body())->records;
    }


    public function fundTransfer(string $fromUserId, array $recipient, bool $requireOtp = true)
    {
        try {
            DB::beginTransaction();

            $user = $this->users->getUser($fromUserId);
            $this->transactionValidationService->validateUser($user);

            $serviceFee = $this->serviceFees
                ->getByTierAndTransCategory($user->tier_id, TransactionCategoryIds::send2BankPesoNet);

            $serviceFeeId = $serviceFee ? $serviceFee->id : '';
            $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
            $totalAmount = $recipient['amount'] + $serviceFeeAmount;

            $this->transactionValidationService
                ->validate($user, TransactionCategoryIds::send2BankPesoNet, $totalAmount);

            $userFullName = ucwords($user->profile->full_name);
            $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);
            $currentDate = Carbon::now();
            $transactionDate = $currentDate->toDateTimeLocalString('millisecond');
            $notifyType = $this->getRecepientField($recipient);
            $notifyTo = $notifyType !== '' ? $recipient[$notifyType] : '';

            $send2Bank = $this->updateUserTransactions($user->id, $user->tier_id, $refNo, $recipient['account_name'],
                $recipient['account_number'], $recipient['message'], $recipient['amount'], $serviceFeeAmount,
                $serviceFeeId, $currentDate, $notifyType, $notifyTo);

            if (!$send2Bank) $this->transFailed();

            $this->updateUserBalance($user->id, $totalAmount);

            $transferResponse = $this->ubpService->fundTransfer($refNo, $userFullName, $recipient['bank_code'],
                $recipient['account_number'], $recipient['account_name'], $recipient['amount'], $transactionDate,
                $recipient['message'], TpaProviders::ubpPesonet);

            $this->handleResponse($send2Bank, $transferResponse);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function updateUserBalance(string $userId, float $amount)
    {
        $balanceInfo = $this->userBalances->getByUserAccountID($userId);

        if ($balanceInfo->available_balance < $amount) $this->userInsufficientBalance();
        $balanceInfo->available_balance -= $amount;
        $balanceInfo->save();
    }

    private function updateUserTransactions(string $userId, string $tierId, string $refNo, string $accountName,
                                            string $accountNumber, string $message, float $amount, float $serviceFeeAmount,
                                            string $serviceFeeId, Carbon $transactionDate, string $notifyType, string $notifyTo)
    {
        $transactionCategoryId = TransactionCategoryIds::send2BankPesoNet;
        $totalAmount = $amount + $serviceFeeAmount;

        $send2bank = $this->send2banks->createTransaction($userId, $refNo, $accountName, $accountNumber, $message, $amount,
            $serviceFeeAmount, $serviceFeeId, $transactionDate, $transactionCategoryId, TpaProviders::ubpPesonet,
            $notifyType, $notifyTo, $userId);

        $transaction = $this->transactionHistories->log($userId, $transactionCategoryId, $send2bank->id, $refNo,
            $send2bank->total_amount, $userId);

        return $send2bank;
    }

    private function handleResponse(OutSend2Bank $send2Bank, Response $response)
    {
        if (!$response->successful()) {
            $errors = $response->json();
            $this->transFailed();
        } else {
            $data = $response->json();
            $code = $data['code'];

            $provider = TpaProviders::ubpPesonet;
            $status = '';
            $providerTransactionId = $data['ubpTranId'];
            $providerRemittanceId = $data['remittanceId'];

            if ($code === UbpResponseCodes::receivedRequest || $code === UbpResponseCodes::processing) {
                $status = TransactionStatuses::pending;
            } elseif ($code === UbpResponseCodes::successfulTransaction) {
                $status = TransactionStatuses::success;
            } else {
                $this->transFailed();
            }

            $send2Bank->status = $status;
            $send2Bank->provider = $provider;
            $send2Bank->provider_transaction_id = $providerTransactionId;
            $send2Bank->provider_remittance_id = $providerRemittanceId;
            $send2Bank->user_updated = $send2Bank->user_account_id;
            $send2Bank->transaction_response = json_encode($data);
            $send2Bank->save();
        }
    }


}
