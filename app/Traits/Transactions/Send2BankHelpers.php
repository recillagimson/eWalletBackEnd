<?php


namespace App\Traits\Transactions;


use App\Enums\SecBankInstapayReturnCodes;
use App\Enums\SecBankPesonetStatus;
use App\Enums\TpaProviders;
use App\Enums\TransactionStatuses;
use App\Enums\UbpResponseCodes;
use App\Enums\UbpResponseStates;
use App\Enums\UsernameTypes;
use App\Models\OutSend2Bank;
use App\Models\UserAccount;
use App\Models\UserBalanceInfo;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\XML\XmlService;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Errors\WithUserErrors;
use App\Traits\UserHelpers;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Log;

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
        if ($provider === TpaProviders::secBankInstapay) return 'SecBank: Instapay';
        if ($provider === TpaProviders::secBankPesonet) return 'SecBank: Pesonet';

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
            Log::error('Send2Bank UBP Error: ', $errors);
            $this->transactionFailed();
        } else {
            $data = $response->json();
            $state = $data['state'];

            $provider = $send2Bank->provider;
            $status = '';
            $providerTransactionId = $data['ubpTranId'];
            $providerRemittanceId = $provider === TpaProviders::ubpPesonet ? $data['remittanceId'] : $data['traceNo'];

            if ($state === UbpResponseStates::receivedRequest || $state === UbpResponseStates::sentForProcessing
                || $state === UbpResponseStates::forConfirmation) {
                $status = TransactionStatuses::pending;
            } elseif ($state === UbpResponseStates::creditedToAccount) {
                $status = TransactionStatuses::success;
            } else {
                Log::info('Send2Bank Transaction Failed:', $data);
                $this->transactionFailed();
            }

            $send2Bank->status = $status;
            $send2Bank->provider_transaction_id = $providerTransactionId;
            $send2Bank->provider_remittance_id = $providerRemittanceId;
            $send2Bank->user_updated = $send2Bank->user_account_id;
            $send2Bank->transaction_response = json_encode($data);
            $send2Bank->save();

            if ($status === TransactionStatuses::success) {
                $this->transactionHistories->log($send2Bank->user_account_id,
                    $send2Bank->transaction_category_id, $send2Bank->id, $send2Bank->reference_number,
                    $send2Bank->total_amount, $send2Bank->transaction_date,
                    $send2Bank->user_account_id);
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

            if ($send2Bank->status === TransactionStatuses::success) {
                $this->transactionHistories->log($send2Bank->user_account_id,
                    $send2Bank->transaction_category_id, $send2Bank->id, $send2Bank->reference_number,
                    $send2Bank->total_amount, $send2Bank->transaction_date, $send2Bank->user_account_id);
            }

            return $send2Bank;
        }
    }

    private function handleSecBankTransferResponse(OutSend2Bank $send2Bank, Response $response): OutSend2Bank
    {
        if ($send2Bank->provider === TpaProviders::secBankInstapay) {
            return $this->handleInstapayTransferResponse($send2Bank, $response);
        }

        if ($send2Bank->provider === TpaProviders::secBankPesonet) {
            return $this->handlePesonetTransferResponse($send2Bank, $response);
        }
    }

    private function handleSecBankStatusResponse(OutSend2Bank $send2Bank, Response $response): OutSend2Bank
    {
        if ($send2Bank->provider === TpaProviders::secBankInstapay) {
            return $this->handleInstapayCheckStatusResponse($send2Bank, $response);
        }

        return $send2Bank;
    }

    private function handleDirectTransferResponse(OutSend2Bank $send2Bank, Response $response): OutSend2Bank
    {
        if (!$response->successful()) {
            $errors = $response->json();
            Log::error('Send2Bank UBP Error: ', $errors);
            $this->transactionFailed();
        } else {
            $data = $response->json();
            $code = $data['code'];

            $provider = TpaProviders::ubp;
            $providerTransactionId = $data['ubpTranId'];
            $providerRemittanceId = $data['uuid'];

            if ($code === UbpResponseCodes::successfulTransaction) {
                $send2Bank->status = TransactionStatuses::success;
            } else if($code === UbpResponseCodes::receivedRequest || UbpResponseCodes::processing || UbpResponseCodes::forConfirmation) {
                $send2Bank->status = TransactionStatuses::pending;
            } else {
                $send2Bank->status = TransactionStatuses::failed;
            }

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

            $this->createAppNotification($user->id, $send2Bank->reference_number, $send2Bank->account_number, $send2Bank->amount,
                $send2Bank->transaction_date, $send2Bank->service_fee, $availableBalance, $send2Bank->provider,
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
            'transaction_number' => $send2Bank->reference_number,
            'service_fee' => $send2Bank->service_fee,
            'transaction_date' => $send2Bank->transaction_date,
            'remarks' => $send2Bank->remarks,
            'particulars' => $send2Bank->particulars
        ];
    }

    private function handleInstapayTransferResponse(OutSend2Bank $send2Bank, Response $response): OutSend2Bank
    {
        $xmlService = new XmlService();
        $xmlBody = $xmlService->toArray($response->body());
        $xmlResponse = $xmlBody['payBankResponse'];
        $xmlReturn = $xmlResponse['payBankReturn'];

        $strData = Str::of($xmlReturn)->explode('|');
        $data = [
            'returnCode' => $strData[0],
            'returnValue' => $strData[1],
            'returnLocalRefId' => $strData[2],
            'traceNo' => $strData[3],
            'returnDupRespCode' => $strData[4],
            'returnDupRespCodeMsg' => $strData[5],
            'returnDupLocalRefID' => $strData[6]
        ];

        if ($data['returnCode'] === SecBankInstapayReturnCodes::success) {
            $send2Bank->status = TransactionStatuses::success;
            $send2Bank->provider_transaction_id = $data['returnLocalRefId'];
            $send2Bank->provider_remittance_id = $data['returnLocalRefId'];
            $send2Bank->user_updated = $send2Bank->user_account_id;
            $send2Bank->transaction_response = json_encode($data);
            $send2Bank->save();

            $this->transactionHistories->log($send2Bank->user_account_id,
                $send2Bank->transaction_category_id, $send2Bank->id, $send2Bank->reference_number,
                $send2Bank->total_amount, $send2Bank->transaction_date,
                $send2Bank->user_account_id);

        }

        return $send2Bank;
    }

    private function handleInstapayCheckStatusResponse(OutSend2Bank $send2Bank, Response $response): OutSend2Bank
    {
        if ($response->successful()) {
            //TODO: PROCEDURE TO UPDATE INSTAPAYS PENDING TRANSACTIONS
        }

        return $send2Bank;
    }

    private function handlePesonetTransferResponse(OutSend2Bank $send2Bank, Response $response): OutSend2Bank
    {
        $data = $response->json();
        if (!$data) $this->transactionFailed();

        if ($data['status'] === SecBankPesonetStatus::success) {
            $send2Bank->status = TransactionStatuses::success;
            $send2Bank->provider_transaction_id = $data['localRefId'];
            $send2Bank->provider_remittance_id = $data['localRefId'];
            $send2Bank->transaction_response = json_encode($data);

            $this->transactionHistories->log($send2Bank->user_account_id,
                $send2Bank->transaction_category_id, $send2Bank->id, $send2Bank->reference_number,
                $send2Bank->total_amount, $send2Bank->transaction_date,
                $send2Bank->user_account_id);
        } else if ($data['status'] === SecBankPesonetStatus::pending) {
            $send2Bank->status = TransactionStatuses::pending;
        } else {
            $this->transactionFailed();
        }

        $send2Bank->user_updated = $send2Bank->user_account_id;
        $send2Bank->transaction_response = json_encode($data);
        $send2Bank->save();

        return $send2Bank;
    }

    public function createAppNotification(string $userId, string $refNo, string $accountNo, float $amount,
                                          Carbon $transactionDate, float $serviceFee, float $newBalance, string $provider,
                                          string $remittanceId)
    {
        $hideAccountNo = Str::substr($accountNo, 0, -4);
        $strAmount = $this->formatAmount($amount);
        $strServiceFee = $this->formatAmount($serviceFee);
        $strNewBalance = $this->formatAmount($newBalance);
        $strDate = $this->formatDate($transactionDate);
        $strProvider = $this->getSend2BankProviderCaption($provider);

        $title = 'SquidPay - Send to Bank Notification';
        $description = 'You have sent P' . $strAmount . ' of SquidPay on ' . $strDate . ' to the account ending in '
            . $hideAccountNo . '. Service Fee for this transaction is P' . $strServiceFee . '. Your new balance is P'
            . $strNewBalance . ' with SquidPay Ref. No. ' . $refNo . ' & ' . $strProvider . ' Remittance No. ' . $remittanceId
            . '. Thank you for using SquidPay!';

        $this->notificationRepository->create([
            'title' => $title,
            'status' => '1',
            'description' => $description,
            'user_account_id' => $userId,
            'user_created' => $userId
        ]);
    }
}
