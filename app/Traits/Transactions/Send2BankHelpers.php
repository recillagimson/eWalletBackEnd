<?php


namespace App\Traits\Transactions;


use App\Enums\TpaProviders;
use App\Models\UserAccount;
use App\Traits\UserHelpers;
use Illuminate\Support\Str;
use App\Enums\UsernameTypes;
use App\Models\OutSend2Bank;
use App\Enums\UbpResponseCodes;
use App\Models\UserBalanceInfo;
use App\Enums\UbpResponseStates;
use App\Enums\TransactionStatuses;
use App\Traits\Errors\WithTpaErrors;
use Illuminate\Http\Client\Response;
use App\Traits\Errors\WithUserErrors;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

trait Send2BankHelpers
{
    use WithUserErrors, UserHelpers, WithTpaErrors;

    private IOutSend2BankRepository $send2banks;
    private IUserTransactionHistoryRepository $transactionHistories;
    private ISmsService $smsService;
    private IEmailService $emailService;

    private function getSend2BankProviderCaption(string $provider): string
    {
        if ($provider === TpaProviders::ubpPesonet) return 'UBP: Pesonet';
        if ($provider === TpaProviders::ubpInstapay) return 'UBP: Instapay';
        if ($provider === TpaProviders::ubp) return 'UBP';

        $this->tpaInvalidProvider();
    }

    private function updateUserBalance(UserBalanceInfo $balanceInfo, float $amount,
                                       string $status): UserBalanceInfo
    {
        if ($status === TransactionStatuses::success) {
            if ($balanceInfo->pending_balance > 0) $balanceInfo->pending_balance -= $amount;
        }

        if ($status === TransactionStatuses::failed) {
            $balanceInfo->available_balance += $amount;
            $balanceInfo->pending_balance -= $amount;
        }

        $balanceInfo->save();
        return $balanceInfo;
    }

    private function handleTransferResponse(OutSend2Bank $send2Bank, Response $response): OutSend2Bank
    {
        if (!$response->successful()) {
            $errors = $response->json();
            $this->transactionFailed();
        } else {
            $data = $response->json();
            $state = $data['state'];

            $provider = TpaProviders::ubpPesonet;
            $status = '';
            $providerTransactionId = $data['ubpTranId'];
            $providerRemittanceId = $data['remittanceId'];

            if ($state === UbpResponseStates::receivedRequest || $state === UbpResponseStates::sentForProcessing
                || $state === UbpResponseStates::forConfirmation) {
                $status = TransactionStatuses::pending;
            } elseif ($state === UbpResponseStates::creditedToAccount) {
                $status = TransactionStatuses::success;
            } else {
                $this->transactionFailed();
            }

            $send2Bank->status = $status;
            $send2Bank->provider = $provider;
            $send2Bank->provider_transaction_id = $providerTransactionId;
            $send2Bank->provider_remittance_id = $providerRemittanceId;
            $send2Bank->user_updated = $send2Bank->user_account_id;
            $send2Bank->transaction_response = json_encode($data);
            $send2Bank->save();

            if ($status === TransactionStatuses::success) {
                $this->transactionHistories->log($send2Bank->user_account_id,
                    $send2Bank->transaction_category_id, $send2Bank->id, $send2Bank->reference_number,
                    $send2Bank->total_amount, $send2Bank->user_account_id);
            }

            return $send2Bank;
        }
    }

    private function handleStatusResponse(OutSend2Bank $send2Bank, Response $response)
    {
        if (!$response->successful()) {
            return;
        } else {
            $data = $response->json()['record'];
            $state = $data['state'];

            if ($state === UbpResponseStates::receivedRequest || $state === UbpResponseStates::sentForProcessing
                || $state === UbpResponseStates::forConfirmation || $state === UbpResponseStates::networkIssue) {
                return $send2Bank;
            }

            if ($state === UbpResponseStates::creditedToAccount) {
                $send2Bank->status = TransactionStatuses::success;
            }

            if ($state === UbpResponseStates::failedToCreditAccount) {
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
        if (!$response->successful()) {
            //$errors = $response->json();
            $this->transFailed();
        } else {
            $data = $response->json();

            $provider = TpaProviders::ubpDirect;
            $status = '';
            $providerTransactionId = $data['ubpTranId'];
            $providerRemittanceId = $data['uuid'];

            $state = $data['state'];

            if ($state === UbpResponseStates::receivedRequest || $state === UbpResponseStates::sentForProcessing
                || $state === UbpResponseStates::forConfirmation || $state === UbpResponseStates::networkIssue) {
                return $send2Bank;
            }

            if ($state === UbpResponseStates::creditedToAccount) {
                $send2Bank->status = TransactionStatuses::success;
            }

            if ($state === UbpResponseStates::failedToCreditAccount) {
                $send2Bank->status = TransactionStatuses::failed;
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
    private function sendNotifications(UserAccount $user, OutSend2Bank $send2Bank, float $availableBalance)
    {
        $usernameField = $this->getUsernameFieldByAvailability($user);
        $username = $this->getUsernameByField($user, $usernameField);
        $notifService = $usernameField === UsernameTypes::Email ? $this->emailService : $this->smsService;

        if ($send2Bank->status === TransactionStatuses::success) {
            $notifService->sendSend2BankSenderNotification($username, $send2Bank->reference_number, $send2Bank->account_number,
                $send2Bank->amount, $send2Bank->transaction_date, $send2Bank->service_fee, $availableBalance, $send2Bank->provider,
                $send2Bank->provider_remittance_id);

            if ($send2Bank->send_receipt_to) {
                $this->emailService->sendSend2BankReceipt($send2Bank->send_receipt_to, $send2Bank);
            }
        }
    }

    private function createTransferResponse(OutSend2Bank $send2Bank): array
    {
        return [
            'bank_name' => $send2Bank->bank_name,
            'account_number' => $send2Bank->account_number,
            'account_name' => $send2Bank->account_name,
            'amount' => $send2Bank->amount,
            'send_receipt_to' => $send2Bank->send_receipt_to,
            'purpose' => Str::lower($send2Bank->purpose) === 'others' ? $send2Bank->other_purpose : $send2Bank->purpose,
            'transaction_number' => $send2Bank->reference_number
        ];
    }
}
