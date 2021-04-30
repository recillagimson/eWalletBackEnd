<?php


namespace App\Traits\Transactions;


use App\Enums\TpaProviders;
use App\Enums\TransactionStatuses;
use App\Enums\UbpResponseCodes;
use App\Models\OutSend2Bank;
use App\Models\UserBalanceInfo;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Traits\Errors\WithUserErrors;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;

trait Send2BankHelpers
{
    use WithUserErrors;

    private IOutSend2BankRepository $send2banks;
    private IUserTransactionHistoryRepository $transactionHistories;

    private function updateUserBalance(UserBalanceInfo $balanceInfo, float $amount): UserBalanceInfo
    {
        if ($balanceInfo->available_balance < $amount) $this->userInsufficientBalance();
        $balanceInfo->available_balance -= $amount;
        $balanceInfo->save();

        return $balanceInfo;
    }

    private function updateUserTransactions(string $userId, string $refNo, string $accountName,
                                            string $accountNumber, string $message, float $amount, float $serviceFeeAmount,
                                            string $serviceFeeId, Carbon $transactionDate, string $notifyType,
                                            string $notifyTo, string $transactionCategoryId, string $provider)
    {
        $send2bank = $this->send2banks->createTransaction($userId, $refNo, $accountName, $accountNumber, $message, $amount,
            $serviceFeeAmount, $serviceFeeId, $transactionDate, $transactionCategoryId, $provider,
            $notifyType, $notifyTo, $userId);

        $this->transactionHistories->log($userId, $transactionCategoryId, $send2bank->id, $refNo,
            $send2bank->total_amount, $userId);

        return $send2bank;
    }

    private function handleTransferResponse(OutSend2Bank $send2Bank, Response $response): OutSend2Bank
    {
        if (!$response->successful()) {
            //$errors = $response->json();
            $this->transFailed();
        } else {
            $data = $response->json();
            $code = $data['code'];

            $provider = TpaProviders::ubpPesonet;
            $status = '';
            $providerTransactionId = $data['ubpTranId'];
            $providerRemittanceId = $data['remittanceId'];

            if ($code === UbpResponseCodes::receivedRequest || $code === UbpResponseCodes::processing
                || $code === UbpResponseCodes::forConfirmation) {
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

            return $send2Bank;
        }
    }

    private function handleStatusResponse(OutSend2Bank $send2Bank, Response $response)
    {
        if (!$response->successful()) {
            return;
        } else {
            $data = $response->json()['record'];
            $code = $data['code'];

            if ($code === UbpResponseCodes::successfulTransaction) {
                $send2Bank->status = TransactionStatuses::success;
            }

            if ($code === UbpResponseCodes::failedTransaction) {
                $send2Bank->status = TransactionStatuses::failed;
            }

            $send2Bank->user_updated = $send2Bank->user_account_id;
            $send2Bank->transaction_response = json_encode($data);
            $send2Bank->save();

            return $send2Bank;
        }
    }

    private function handleDirectTransferResponse(OutSend2Bank $send2Bank, Response $response): OutSend2Bank
    {
        \Log::info($response->json());
        if (!$response->successful()) {
            //$errors = $response->json();
            $this->transFailed();
        } else {
            $data = $response->json();
            $code = $data['code'];

            $provider = TpaProviders::ubpDirect;
            $status = '';
            $providerTransactionId = $data['ubpTranId'];
            $providerRemittanceId = $data['uuid'];

            if ($code === UbpResponseCodes::receivedRequest || $code === UbpResponseCodes::processing
                || $code === UbpResponseCodes::forConfirmation) {
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

            return $send2Bank;
        }
    }
}
