<?php


namespace App\Services\Send2Bank;


use Exception;
use Carbon\Carbon;
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

    public function __construct(IUBPService $ubpService, IReferenceNumberService $referenceNumberService,
                                ITransactionValidationService $transactionValidationService,
                                INotificationService $notificationService, ISmsService $smsService,
                                IEmailService $emailService,
                                IUserAccountRepository $users, IUserBalanceInfoRepository $userBalances,
                                IOutSend2BankRepository $send2banks, IServiceFeeRepository $serviceFees,
                                IUserTransactionHistoryRepository $transactionHistories)
    {
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
    public function fundTransferToUBPDirect(string $fromUserId, array $recipient, bool $requireOtp = true) {
        DB::beginTransaction();
        try {

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
            
            $send2Bank = $this->updateUserTransactions($user->id, $refNo, $recipient['recipientName'],
                $recipient['accountNo'], $recipient['remarks'], $recipient['amount'], $serviceFeeAmount,
                $serviceFeeId, $currentDate, $notifyType, $notifyTo, TransactionCategoryIds::send2BankUBP,
                TpaProviders::ubpDirect);
                

                if (!$send2Bank) $this->transFailed();

                $balanceInfo = $this->updateUserBalance($user->balanceInfo, $totalAmount);

            $transferResponse = $this->ubpService->send2BankUBPDirect($refNo, $transactionDate, $recipient['accountNo'], $recipient['amount'], $recipient['remarks'], $recipient['particulars'], $recipient['recipientName']);
            $send2Bank = $this->handleDirectTransferResponse($send2Bank, $transferResponse);
            // $this->sendNotifications($user, $send2Bank, $balanceInfo->available_balance);
            DB::commit();
        }    
        catch(\Exception $e) {
            DB::rollback();
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
