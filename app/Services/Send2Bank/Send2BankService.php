<?php


namespace App\Services\Send2Bank;


use App\Enums\ReferenceNumberTypes;
use App\Enums\SquidPayModuleTypes;
use App\Enums\TpaProviders;
use App\Enums\TransactionCategories;
use App\Enums\TransactionStatuses;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\ProviderBanks\IProviderBanksRepository;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\ThirdParty\SecurityBank\ISecurityBankService;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Services\Utilities\XML\XmlService;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Errors\WithUserErrors;
use App\Traits\StringHelpers;
use App\Traits\Transactions\Send2BankHelpers;
use App\Traits\UserHelpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Log;

class Send2BankService implements ISend2BankService
{
    use WithAuthErrors, WithUserErrors, WithTpaErrors, WithTransactionErrors;
    use UserHelpers, Send2BankHelpers, StringHelpers;

    private IReferenceNumberService $referenceNumberService;
    private ITransactionValidationService $transactionValidationService;
    private INotificationService $notificationService;
    private IUBPService $ubpService;
    private ISecurityBankService $secBankService;
    private IOtpService $otpService;
    private ILogHistoryService $logHistories;

    private IUserAccountRepository $users;
    private IUserBalanceInfoRepository $userBalances;
    private IServiceFeeRepository $serviceFees;

    protected string $transactionCategoryId;
    protected string $provider;
    private IProviderBanksRepository $providerBanks;

    public function __construct(IUBPService                       $ubpService,
                                ISecurityBankService              $secBankService,
                                IReferenceNumberService           $referenceNumberService,
                                ITransactionValidationService     $transactionValidationService,
                                INotificationService              $notificationService,
                                ISmsService                       $smsService,
                                IEmailService                     $emailService,
                                IOtpService                       $otpService,
                                ILogHistoryService                $logHistories,
                                IUserAccountRepository            $users,
                                IUserBalanceInfoRepository        $userBalances,
                                IOutSend2BankRepository           $send2banks,
                                IServiceFeeRepository             $serviceFees,
                                IUserTransactionHistoryRepository $transactionHistories,
                                IProviderBanksRepository          $providerBanks,
                                INotificationRepository           $notificationRepository)
    {
        $this->ubpService = $ubpService;
        $this->referenceNumberService = $referenceNumberService;
        $this->transactionValidationService = $transactionValidationService;
        $this->notificationService = $notificationService;
        $this->smsService = $smsService;
        $this->emailService = $emailService;
        $this->otpService = $otpService;
        $this->providerBanks = $providerBanks;

        $this->users = $users;
        $this->userBalances = $userBalances;
        $this->serviceFees = $serviceFees;
        $this->send2banks = $send2banks;
        $this->transactionHistories = $transactionHistories;
        $this->secBankService = $secBankService;
        $this->logHistories = $logHistories;
        $this->notificationRepository = $notificationRepository;
    }


    public function getBanks(): array
    {
        if ($this->provider === TpaProviders::secBankInstapay) {
            $response = $this->secBankService->getBanks($this->provider);
            if (!$response->successful()) $this->tpaErrorOccured($this->getSend2BankProviderCaption($this->provider));

            $xmlService = new XmlService();
            $xmlBody = $xmlService->toArray($response->body());
            $xmlResponse = $xmlBody['getListOfBanksResponse'];

            $banksColl = collect(array_values($xmlResponse));
            $banks = $banksColl->map(function ($item) {
                $bank = Str::of($item)->explode('-', 2);
                return [
                    'code' => $bank[0],
                    'bank' => $bank[1]
                ];
            });

            return $banks->sortBy('bank')->all();
        }

        if ($this->provider === TpaProviders::secBankPesonet) {
            $bankCollection = $this->providerBanks->getPesonetBanks();

            $banks = $bankCollection->map(function ($item) {
                return [
                    'code' => $item->code,
                    'bank' => $item->name,
                ];
            });

            return $banks->sortBy('bank')->all();
        }

        $response = $this->ubpService->getBanks($this->provider);
        if (!$response->successful()) $this->tpaErrorOccured($this->getSend2BankProviderCaption($this->provider));
        $banks = collect(json_decode($response->body())->records);

        return $banks->sortBy('bank')->values()->all();
    }

    public function getPurposes(): array
    {
        if ($this->provider === TpaProviders::ubpPesonet) return [];

        $response = $this->ubpService->getPurposes();
        if (!$response->successful()) $this->tpaErrorOccured($this->getSend2BankProviderCaption($this->provider));
        return json_decode($response->body())->records;
    }

    public function validateFundTransfer(string $userId, array $recipient): array
    {
        $user = $this->users->getUser($userId);
        $this->transactionValidationService->validateUser($user);

        $serviceFee = $this->serviceFees
            ->getByTierAndTransCategory($user->tier_id, $this->transactionCategoryId);


        $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
        $totalAmount = $recipient['amount'] + $serviceFeeAmount;


        $this->transactionValidationService->checkUserBalance($user, $totalAmount );

        $this->transactionValidationService
            ->validate($user, $this->transactionCategoryId, $totalAmount);

        return [
            'service_fee' => $serviceFeeAmount
        ];
    }

    public function fundTransfer(string $userId, array $data, bool $requireOtp = true): array
    {
        $updateReferenceCounter = false;

        try {
            DB::beginTransaction();

            $user = $this->users->getUser($userId);
            $this->transactionValidationService->validateUser($user);

            $serviceFee = $this->serviceFees
                ->getByTierAndTransCategory($user->tier_id, $this->transactionCategoryId);

            $serviceFeeId = $serviceFee ? $serviceFee->id : '';
            $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
            $totalAmount = $data['amount'] + $serviceFeeAmount;

            $this->transactionValidationService->checkUserBalance($user, $totalAmount);

            $userFullName = ucwords($user->profile->full_name);
            $recipientFullName = ucwords($data['account_name'] ?: $data['recipient_first_name'] . ' ' . $data['recipient_last_name']);
            $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);
            $currentDate = Carbon::now();
            $transactionDate = $currentDate->toDateTimeLocalString('millisecond');
            $otherPurpose = $data['other_purpose'] ?? '';

            $send2Bank = $this->send2banks->createTransaction($userId, $refNo, $data['bank_code'], $data['bank_name'],
                $recipientFullName, $data['account_number'], $data['purpose'], $otherPurpose,
                $data['amount'], $serviceFeeAmount, $serviceFeeId, $currentDate, $this->transactionCategoryId, $this->provider,
                $data['send_receipt_to'], $userId);

            if (!$send2Bank) $this->transactionFailed();

            $data['sender_first_name'] = $user->profile->first_name;
            $data['sender_last_name'] = $user->profile->last_name;
            $data['refNo'] = $refNo;

            if ($this->provider === TpaProviders::secBankInstapay || $this->provider === TpaProviders::secBankPesonet) {
                $transferResponse = $this->secBankService->fundTransfer($this->provider, $data);

                $updateReferenceCounter = true;
                $send2Bank = $this->handleSecBankTransferResponse($send2Bank, $transferResponse);
            } else {
                $transferResponse = $this->ubpService->fundTransfer($refNo, $userFullName, $user->profile->postal_code,
                    $data['bank_code'], $data['account_number'], $recipientFullName, $data['amount'],
                    $transactionDate, ' ', $this->provider);

                $updateReferenceCounter = true;
                $send2Bank = $this->handleTransferResponse($send2Bank, $transferResponse);
            }

            $balanceInfo = $user->balanceInfo;
            $balanceInfo->available_balance -= $totalAmount;
            if ($send2Bank->status === TransactionStatuses::pending) $balanceInfo->pending_balance += $totalAmount;
            if ($send2Bank->status === TransactionStatuses::failed) $balanceInfo->available_balance += $totalAmount;
            $balanceInfo->save();

            DB::commit();
            $this->sendNotifications($user, $send2Bank, $balanceInfo->available_balance);
            $this->logHistory($userId, $refNo, $currentDate, $totalAmount, $send2Bank->account_number);

            if ($send2Bank->status === TransactionStatuses::failed) $this->transactionFailed();
            return $this->createTransferResponse($send2Bank);
        } catch (Exception $e) {
            DB::rollBack();

            if ($updateReferenceCounter === true)
                $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);

            Log::error('Send 2 Bank Error: ', $e->getTrace());
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
            $response = $this->ubpService->checkStatus($send2Bank->provider, $send2Bank->reference_number);

            if ($send2Bank->provider === TpaProviders::secBankInstapay || $send2Bank->provider === TpaProviders::secBankPesonet)
                $send2Bank = $this->handleSecBankStatusResponse($send2Bank, $response);
            else
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

    public function processAllPending()
    {
        $users = $this->send2banks->getUsersWithPending();

        foreach ($users as $user) {
            Log::info('S2B Processing User:', ['user_account_id' => $user->user_account_id]);
            $this->processPending($user->user_account_id);
        }
    }

    public function updateTransaction(string $status, string $refNo)
    {
        $send2Bank = $this->send2banks->getByReferenceNo($refNo);
        if (!$send2Bank) $this->transactionNotFound();

        $response = $this->ubpService->updateTransaction($status, $send2Bank->provider_remittance_id);
        if ($response->successful()) return $response->json();

        $errors = $response->json();
        Log::error('Send2Bank UBP Error', $errors);
        $this->transactionFailed();
    }

    public function logHistory(string $userId, string $refNo, Carbon $logDate, float $amount, string $accountNo)
    {
        $spModule = $this->provider === TpaProviders::secBankInstapay ?
            SquidPayModuleTypes::send2BankInstapay :
            SquidPayModuleTypes::send2BankPesonet;

        $operation = $this->provider === TpaProviders::secBankInstapay ?
            TransactionCategories::Send2BankInstaPay :
            TransactionCategories::Send2BankPesoNet;

        $remarks = "Sent $amount to account number: $accountNo.";

        $this->logHistories->logUserHistory($userId, $refNo, $spModule,
            null, $logDate, $remarks, $operation);
    }

}
