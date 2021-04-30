<?php


namespace App\Services\Send2Bank;


use App\Enums\ReferenceNumberTypes;
use App\Enums\TpaProviders;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Enums\UsernameTypes;
use App\Models\OutSend2Bank;
use App\Models\UserAccount;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
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

class Send2BankPesonetService implements ISend2BankService
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

            $send2Bank = $this->updateUserTransactions($user->id, $refNo, $recipient['account_name'],
                $recipient['account_number'], $recipient['message'], $recipient['amount'], $serviceFeeAmount,
                $serviceFeeId, $currentDate, $notifyType, $notifyTo, TransactionCategoryIds::send2BankPesoNet,
                TpaProviders::ubpPesonet);

            if (!$send2Bank) $this->transFailed();

            $balanceInfo = $this->updateUserBalance($user->balanceInfo, $totalAmount);

            $transferResponse = $this->ubpService->fundTransfer($refNo, $userFullName, $recipient['bank_code'],
                $recipient['account_number'], $recipient['account_name'], $recipient['amount'], $transactionDate,
                $recipient['message'], TpaProviders::ubpPesonet);

            $send2Bank = $this->handleTransferResponse($send2Bank, $transferResponse);
            $this->sendNotifications($user, $send2Bank, $balanceInfo->available_balance);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function processPending(string $userId)
    {
        $user = $this->users->getUser($userId);
        $pendingSend2Banks = $this->send2banks->getPending($userId);
        //$processedCount = 0;

        foreach ($pendingSend2Banks as $send2Bank) {
            $response = $this->ubpService->checkStatus(TpaProviders::ubpPesonet, $send2Bank->reference_number);
            $send2Bank = $this->handleStatusResponse($send2Bank, $response);
            $this->sendNotifications($user, $send2Bank, $user->balanceInfo->available_balance);
        }
    }

    private function sendNotifications(UserAccount $user, OutSend2Bank $send2Bank, float $userBalance)
    {
        $usernameField = $this->getUsernameFieldByAvailability($user);
        $username = $this->getUsernameByField($user, $usernameField);
        $notifService = $usernameField === UsernameTypes::Email ? $this->emailService : $this->smsService;

        if ($send2Bank->status === TransactionStatuses::success) {
            $notifService->sendSend2BankSenderNotification($username, $send2Bank->reference_number, $send2Bank->account_number,
                $send2Bank->amount, $send2Bank->transaction_date, $send2Bank->service_fee, $userBalance, $send2Bank->provider,
                $send2Bank->remittance_id);
        }


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
            $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank) . "testing1";
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

}
