<?php


namespace App\Services\Send2Bank;


use Exception;
use Carbon\Carbon;
use App\Enums\OtpTypes;
use App\Enums\TpaProviders;
use App\Models\UserAccount;
use App\Traits\UserHelpers;
use App\Enums\UsernameTypes;
use App\Models\OutSend2Bank;
use App\Enums\UbpResponseCodes;
use App\Enums\TransactionStatuses;
use Illuminate\Support\Facades\DB;
use App\Enums\ReferenceNumberTypes;
use App\Traits\Errors\WithTpaErrors;
use App\Enums\TransactionCategoryIds;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithUserErrors;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Transactions\Send2BankHelpers;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

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

    public function __construct(IUBPService $ubpService, IReferenceNumberService $referenceNumberService,
                                ITransactionValidationService $transactionValidationService,
                                INotificationService $notificationService, ISmsService $smsService,
                                IEmailService $emailService,
                                IUserAccountRepository $users, IUserBalanceInfoRepository $userBalances,
                                IOutSend2BankRepository $send2banks, IServiceFeeRepository $serviceFees,
                                IUserTransactionHistoryRepository $transactionHistories,
                                IOtpService $otpService
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
    }


    // Direct to bank
    public function fundTransferToUBPDirect(string $userId, array $recipient, bool $requireOtp = true) {
        $updateReferenceCounter = false;

        try {
            DB::beginTransaction();
            $transactionCategoryId = TransactionCategoryIds::send2BankPesoNet;
            $provider = TpaProviders::ubpDirect;

            $user = $this->users->getUser($userId);
            $this->transactionValidationService->validateUser($user);

            $serviceFee = $this->serviceFees
                ->getByTierAndTransCategory($user->tier_id, $transactionCategoryId);

            $serviceFeeId = $serviceFee ? $serviceFee->id : '';
            $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
            $totalAmount = $recipient['amount'] + $serviceFeeAmount;

            $this->transactionValidationService
                ->validate($user, $transactionCategoryId, $totalAmount);


            $this->otpService->ensureValidated(OtpTypes::send2Bank . ':' . $userId);
            $userFullName = ucwords($user->profile->full_name);
            $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);
            $currentDate = Carbon::now();
            $transactionDate = $currentDate->toDateTimeLocalString('millisecond');
            $otherPurpose = $recipient['other_purpose'] ?? '';
            
            // SET DEFAULT FOR NOW
            $recipient['bank_code'] = "UBP";
            $recipient['bank_name'] = "Union Bank";


            $send2Bank = $this->send2banks->createTransaction($userId, $refNo, $recipient['bank_code'], $recipient['bank_name'],
            $recipient['recipient_name'], $recipient['recipient_account_no'], $recipient['remarks'], $otherPurpose,
            $recipient['amount'], $serviceFeeAmount, $serviceFeeId, $currentDate, $transactionCategoryId, $provider,
            $recipient['send_receipt_to'], $userId);
            
            if (!$send2Bank) $this->transactionFailed();
            
            
            $transferResponse = $this->ubpService->send2BankUBPDirect($refNo, $transactionDate, $recipient['recipient_account_no'], $totalAmount, $recipient['remarks'], "", $recipient['recipient_name']);

            $updateReferenceCounter = true;
            $send2Bank = $this->handleDirectTransferResponse($send2Bank, $transferResponse);

            $balanceInfo = $user->balanceInfo;
            $balanceInfo->available_balance -= $totalAmount;
            if ($send2Bank->status === TransactionStatuses::pending) $balanceInfo->pending_balance += $totalAmount;
            if ($send2Bank->status === TransactionStatuses::failed) $balanceInfo->available_balance += $totalAmount;
            $balanceInfo->save();

            $this->sendNotifications($user, $send2Bank, $balanceInfo->available_balance);
            DB::commit();

            return $this->createTransferResponse($send2Bank);
        } catch (Exception $e) {
            DB::rollBack();

            if ($updateReferenceCounter === true)
                $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);

            throw $e;
        }
    }

    public function verifyPendingDirectTransactions() {
        $pendingTransactions = $this->send2banks->getPendingDirectTransactionsByAuthUser();
        foreach($pendingTransactions as $transaction) {
            $response = $this->ubpService->verifyPendingDirectTransaction($transaction->reference_number);
            $data = $response->json();

            $code = $data['record']['code'];
            $status = TransactionStatuses::failed;
            if ($code === UbpResponseCodes::receivedRequest || $code === UbpResponseCodes::processing
                || $code === UbpResponseCodes::forConfirmation) {
                $status = TransactionStatuses::pending;
            } elseif ($code === UbpResponseCodes::successfulTransaction) {
                $status = TransactionStatuses::success;
            } else {
                $this->transFailed();
            }
            
            $transaction->update([
                'status' => $status
            ]);
        }
    }

}
