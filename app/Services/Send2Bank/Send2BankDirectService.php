<?php


namespace App\Services\Send2Bank;


use App\Enums\OtpTypes;
use App\Enums\ReferenceNumberTypes;
use App\Enums\SquidPayModuleTypes;
use App\Enums\TpaProviders;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Models\OutSend2Bank;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Errors\WithUserErrors;
use App\Traits\Transactions\Send2BankHelpers;
use App\Traits\UserHelpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class Send2BankDirectService implements ISend2BankDirectService
{
    use WithAuthErrors, WithUserErrors, WithTpaErrors, WithTransactionErrors;
    use UserHelpers, Send2BankHelpers;

    private IReferenceNumberService $referenceNumberService;
    private ITransactionValidationService $transactionValidationService;
    private INotificationService $notificationService;
    private IUBPService $ubpService;
    private ISmsService $smsService;
    private IEmailService $emailService;

    private IUserAccountRepository $users;
    private IUserBalanceInfoRepository $userBalances;
    private IServiceFeeRepository $serviceFees;
    private IOtpService $otpService;

    private ILogHistoryService $logHistoryService;

    public function __construct(IUBPService $ubpService, IReferenceNumberService $referenceNumberService,
                                ITransactionValidationService $transactionValidationService,
                                INotificationService $notificationService, ISmsService $smsService,
                                IEmailService $emailService,
                                IUserAccountRepository $users, IUserBalanceInfoRepository $userBalances,
                                IOutSend2BankRepository $send2banks, IServiceFeeRepository $serviceFees,
                                IUserTransactionHistoryRepository $transactionHistories,
                                IOtpService $otpService, ILogHistoryService $logHistoryService
                                )
                                {
        $this->otpService = $otpService;
        $this->ubpService = $ubpService;
        $this->referenceNumberService = $referenceNumberService;
        $this->transactionValidationService = $transactionValidationService;
        $this->notificationService = $notificationService;

        $this->users = $users;
        $this->userBalances = $userBalances;
        $this->serviceFees = $serviceFees;
        $this->send2banks = $send2banks;
        $this->transactionHistories = $transactionHistories;
        $this->smsService = $smsService;
        $this->emailService = $emailService;


        $this->logHistoryService = $logHistoryService;
    }


    // Direct to bank
    public function fundTransferToUBPDirect(string $userId, array $recipient, bool $requireOtp = true) {
        $updateReferenceCounter = false;

        try {
            DB::beginTransaction();
            $transactionCategoryId = TransactionCategoryIds::send2BankUBP;
            $provider = TpaProviders::ubp;

            $user = $this->users->getUser($userId);
            $this->transactionValidationService->validateUser($user);

            $serviceFee = $this->serviceFees
                ->getByTierAndTransCategory($user->tier_id, $transactionCategoryId);

            $serviceFeeId = $serviceFee ? $serviceFee->id : '';
            $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
            $totalAmount = $recipient['amount'] + $serviceFeeAmount;

            $this->transactionValidationService
                ->validate($user, $transactionCategoryId, $totalAmount);


            $this->otpService->ensureValidated(OtpTypes::send2Bank . ':' . $userId, $user->otp_enabled);
            $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);

            $currentDate = Carbon::now();
            $transactionDate = $currentDate->toDateTimeLocalString('millisecond');
            $otherPurpose = $recipient['other_purpose'] ?? '';

            // SET DEFAULT FOR NOW
            $recipient['bank_code'] = "UBP";
            $recipient['bank_name'] = "Union Bank of the Philippines";


            $send2Bank = $this->send2banks->createTransaction($userId, $refNo, $recipient['bank_code'], $recipient['bank_name'],
            $recipient['recipient_name'], $recipient['recipient_account_no'], $recipient['remarks'], $otherPurpose,
            $recipient['amount'], $serviceFeeAmount, $serviceFeeId, $currentDate, $transactionCategoryId, $provider,
            $recipient['send_receipt_to'], $userId, $recipient['remarks'], $recipient['particulars']);
            if (!$send2Bank) $this->transactionFailed();


            $transferResponse = $this->ubpService->send2BankUBPDirect($refNo, $transactionDate, $recipient['recipient_account_no'], $totalAmount, $recipient['remarks'], "", $recipient['recipient_name']);

            $updateReferenceCounter = true;

            $send2Bank = $this->handleDirectTransferResponse($send2Bank, $transferResponse);
            $balanceInfo = $user->balanceInfo;
            $balanceInfo->available_balance -= $totalAmount;
            if ($send2Bank->status === TransactionStatuses::pending) $balanceInfo->pending_balance += $totalAmount;
            if ($send2Bank->status === TransactionStatuses::failed) $balanceInfo->available_balance += $totalAmount;
            $balanceInfo->save();

            // Create transaction history
            if($send2Bank->status === TransactionStatuses::success) {
                $this->transactionHistories->log($userId, $transactionCategoryId, $send2Bank->id, $refNo,
                    $totalAmount, $send2Bank->transaction_date, request()->user()->id);
            }

            // CREATE LOG HISTORY
            $audit_remarks = request()->user()->account_number . " has transfered " . $totalAmount . " vi UBP Direct";

            $this->logHistoryService->logUserHistory($userId, $refNo, SquidPayModuleTypes::sendMoneyUBPDirect, get_class(new OutSend2Bank()), $transactionDate, $audit_remarks);

            $this->sendNotifications($user, $send2Bank, $balanceInfo->available_balance);
            DB::commit();

            //return $this->createTransferResponse($send2Bank);
        } catch (Exception $e) {
            DB::rollBack();

            if ($updateReferenceCounter === true)
                $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);

            throw $e;
        }
    }

    public function validateFundTransfer(string $userId, array $recipient)
    {
        $user = $this->users->getUser($userId);
        $this->transactionValidationService->validateUser($user);

        $serviceFee = $this->serviceFees
            ->getByTierAndTransCategory($user->tier_id, TransactionCategoryIds::send2BankUBP);

        $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
        $totalAmount = $recipient['amount'] + $serviceFeeAmount;

        $this->transactionValidationService
            ->validate($user, TransactionCategoryIds::send2BankUBP, $totalAmount);

        return [
            'service_fee' => $serviceFeeAmount
        ];
    }

    public function verifyPendingDirectTransactions(string $userId): array
    {
        $user = $this->users->getUser($userId);
        $pendingSend2Banks = $this->send2banks->getPending($userId);
        $successCount = 0;
        $failCount = 0;

        foreach ($pendingSend2Banks as $send2Bank) {
            $response = $this->ubpService->checkStatus(TpaProviders::ubp, $send2Bank->reference_number);
            $send2Bank = $this->handleStatusResponse($send2Bank, $response);
            $balanceInfo = $this->updateUserBalance($user->balanceInfo, $send2Bank->total_amount, $send2Bank->status);
            $this->sendNotifications($user, $send2Bank, $balanceInfo->available_balance);

            if ($send2Bank->status === TransactionStatuses::success) $successCount++;
            if ($send2Bank->status === TransactionStatuses::failed) $failCount++;

            if($send2Bank->status === TransactionStatuses::success) {
                $this->transactionHistories->log($userId, $send2Bank->transaction_category_id, $send2Bank->id,
                    $send2Bank->reference_number, $send2Bank->total_amount, $send2Bank->transaction_date,
                    request()->user()->id);
            }
        }

        return [
            'total_pending_count' => $pendingSend2Banks->count(),
            'success_count' => $successCount,
            'failed_count' => $failCount
        ];
    }

    // public function verifyPendingDirectTransactions() {
    //     $pendingTransactions = $this->send2banks->getPendingDirectTransactionsByAuthUser();
    //     foreach($pendingTransactions as $transaction) {
    //         $response = $this->ubpService->verifyPendingDirectTransaction($transaction->reference_number);
    //         $data = $response->json();

    //         $code = $data['record']['code'];
    //         $status = TransactionStatuses::failed;
    //         if ($code === UbpResponseCodes::receivedRequest || $code === UbpResponseCodes::processing
    //             || $code === UbpResponseCodes::forConfirmation) {
    //             $status = TransactionStatuses::pending;
    //         } elseif ($code === UbpResponseCodes::successfulTransaction) {
    //             $status = TransactionStatuses::success;
    //         }
    //             // } else {
    //         //     $this->transFailed();
    //         // }

    //         $transaction->update([
    //             'status' => $status
    //         ]);
    //     }
    // }

}
