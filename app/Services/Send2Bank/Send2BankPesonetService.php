<?php


namespace App\Services\Send2Bank;


use App\Enums\OtpTypes;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TpaProviders;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
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

class Send2BankPesonetService implements ISend2BankService
{
    use WithAuthErrors, WithUserErrors, WithTpaErrors, WithTransactionErrors;
    use UserHelpers, Send2BankHelpers;

    private IReferenceNumberService $referenceNumberService;
    private ITransactionValidationService $transactionValidationService;
    private INotificationService $notificationService;
    private IUBPService $ubpService;
    private IOtpService $otpService;

    private IUserAccountRepository $users;
    private IUserBalanceInfoRepository $userBalances;
    private IServiceFeeRepository $serviceFees;

    public function __construct(IUBPService $ubpService,
                                IReferenceNumberService $referenceNumberService,
                                ITransactionValidationService $transactionValidationService,
                                INotificationService $notificationService,
                                ISmsService $smsService,
                                IEmailService $emailService,
                                IOtpService $otpService,
                                IUserAccountRepository $users,
                                IUserBalanceInfoRepository $userBalances,
                                IOutSend2BankRepository $send2banks,
                                IServiceFeeRepository $serviceFees,
                                IUserTransactionHistoryRepository $transactionHistories)
    {
        $this->ubpService = $ubpService;
        $this->referenceNumberService = $referenceNumberService;
        $this->transactionValidationService = $transactionValidationService;
        $this->notificationService = $notificationService;
        $this->smsService = $smsService;
        $this->emailService = $emailService;
        $this->otpService = $otpService;

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

    public function validateFundTransfer(string $userId, array $recipient, string $transactionCategoryId = TransactionCategoryIds::send2BankPesoNet)
    {
        // Change to optional param to handle multiple transfer validation
        // set default to TransactionCategoryIds::send2BankPesoNet
        // $transactionCategoryId = TransactionCategoryIds::send2BankPesoNet;

        $user = $this->users->getUser($userId);
        $this->transactionValidationService->validateUser($user);

        $serviceFee = $this->serviceFees
            ->getByTierAndTransCategory($user->tier_id, $transactionCategoryId);

        $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
        $totalAmount = $recipient['amount'] + $serviceFeeAmount;

        $this->transactionValidationService
            ->validate($user, $transactionCategoryId, $totalAmount);
    }

    public function fundTransfer(string $userId, array $recipient, bool $requireOtp = true): array
    {
        $updateReferenceCounter = false;

        try {
            DB::beginTransaction();
            $transactionCategoryId = TransactionCategoryIds::send2BankPesoNet;
            $provider = TpaProviders::ubpPesonet;

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

            $send2Bank = $this->send2banks->createTransaction($userId, $refNo, $recipient['bank_code'], $recipient['bank_name'],
                $recipient['account_name'], $recipient['account_number'], $recipient['purpose'], $otherPurpose,
                $recipient['amount'], $serviceFeeAmount, $serviceFeeId, $currentDate, $transactionCategoryId, $provider,
                $recipient['send_receipt_to'], $userId);

            if (!$send2Bank) $this->transactionFailed();

            $transferResponse = $this->ubpService->fundTransfer($refNo, $userFullName, $recipient['bank_code'],
                $recipient['account_number'], $recipient['account_name'], $recipient['amount'], $transactionDate,
                ' ', $provider);

            $updateReferenceCounter = true;
            $send2Bank = $this->handleTransferResponse($send2Bank, $transferResponse);

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

    public function processPending(string $userId): array
    {
        $user = $this->users->getUser($userId);
        $pendingSend2Banks = $this->send2banks->getPending($userId);
        $successCount = 0;
        $failCount = 0;

        foreach ($pendingSend2Banks as $send2Bank) {
            $response = $this->ubpService->checkStatus(TpaProviders::ubpPesonet, $send2Bank->reference_number);
            $send2Bank = $this->handleStatusResponse($send2Bank, $response);
            $balanceInfo = $this->updateUserBalance($user->balanceInfo, $send2Bank->total_amount, $send2Bank->status);
            $this->sendNotifications($user, $send2Bank, $balanceInfo->available_balance);

            if ($send2Bank->status === TransactionStatuses::success) $successCount++;
            if ($send2Bank->status === TransactionStatuses::failed) $failCount++;
        }

        return [
            'total_pending_count' => $pendingSend2Banks->count(),
            'success_count' => $successCount,
            'failed_count' => $failCount
        ];
    }

    public function updateTransaction(string $status, string $refNo)
    {
        $send2Bank = $this->send2banks->getByReferenceNo($refNo);
        if (!$send2Bank) $this->transactionNotFound();

        $response = $this->ubpService->updateTransaction($status, $send2Bank->provider_remittance_id);
        if ($response->successful()) return $response->json();

        $errors = $response->json();
        $this->transactionFailed();
    }


}
