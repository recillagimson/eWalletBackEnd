<?php


namespace App\Services\BuyLoad;


use App\Enums\AtmPrepaidResponseCodes;
use App\Enums\ReferenceNumberTypes;
use App\Enums\SquidPayModuleTypes;
use App\Enums\TransactionCategories;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Enums\UsernameTypes;
use App\Models\OutBuyLoad;
use App\Models\UserAccount;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\PrepaidLoad\ATM\IAtmService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithBuyLoadErrors;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\UserHelpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuyLoadService implements IBuyLoadService
{
    use WithTransactionErrors, WithBuyLoadErrors;
    use UserHelpers;

    private IAtmService $atmService;
    private IEmailService $emailService;
    private ISmsService $smsService;
    private ITransactionValidationService $transactionValidationService;
    private IOtpService $otpService;
    private IReferenceNumberService $referenceNumberService;
    private ILogHistoryService $logHistoryService;

    private IOutBuyLoadRepository $buyLoads;
    private IUserTransactionHistoryRepository $transactionHistories;
    private IUserAccountRepository $users;

    public function __construct(IAtmService $atmService,
                                IOtpService $otpService,
                                ITransactionValidationService $transactionValidationService,
                                IReferenceNumberService $referenceNumberService,
                                IEmailService $emailService,
                                ISmsService $smsService,
                                IUserAccountRepository $users,
                                IOutBuyLoadRepository $buyLoads,
                                IUserTransactionHistoryRepository $transactionHistories,
                                ILogHistoryService $logHistoryService)
    {
        $this->atmService = $atmService;
        $this->transactionValidationService = $transactionValidationService;
        $this->otpService = $otpService;
        $this->referenceNumberService = $referenceNumberService;
        $this->emailService = $emailService;
        $this->smsService = $smsService;

        $this->users = $users;
        $this->buyLoads = $buyLoads;
        $this->transactionHistories = $transactionHistories;
        $this->logHistoryService = $logHistoryService;
    }

    public function getProductsByProvider(string $mobileNumber): array
    {
        $provider = $this->atmService->getProvider($mobileNumber);
        return array_values($this->atmService->getProductsByProvider($provider)->toArray());
    }

    public function validateLoadTopup(string $userId, string $recipientMobileNumber, string $productCode, string $productName,
                                      float $amount)
    {
        $transactionCategoryId = TransactionCategoryIds::buyLoad;

        $user = $this->users->getUser($userId);
        $this->transactionValidationService->validateUser($user);

        $this->atmService->getProvider($recipientMobileNumber);
        $this->transactionValidationService->validate($user, $transactionCategoryId, $amount);
    }

    public function topupLoad(string $userId, string $recipientMobileNumber, string $productCode, string $productName,
                              float $amount): array
    {
        try {
            DB::beginTransaction();

            $updateReferenceCounter = false;
            $transactionCategoryId = TransactionCategoryIds::buyLoad;

            $user = $this->users->getUser($userId);
            $this->transactionValidationService->validateUser($user);
            $this->transactionValidationService->validate($user, $transactionCategoryId, $amount);

            //$this->otpService->ensureValidated(OtpTypes::buyLoad . ':' . $userId);

            $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::BuyLoad);
            $currentDate = Carbon::now();

            $buyLoad = $this->buyLoads->createTransaction($userId, $refNo, $productCode, $productName, $recipientMobileNumber,
                $amount, $currentDate, $transactionCategoryId, $userId);

            if (!$buyLoad) $this->transactionFailed();

            $buyLoadResponse = $this->atmService->topupLoad($productCode, $recipientMobileNumber, $refNo);

            $updateReferenceCounter = true;
            $buyLoad = $this->handleLoadTopupResponse($buyLoad, $buyLoadResponse);

            $balanceInfo = $user->balanceInfo;
            $balanceInfo->available_balance -= $amount;
            if ($buyLoad->status === TransactionStatuses::pending) $balanceInfo->pending_balance += $amount;
            if ($buyLoad->status === TransactionStatuses::failed) $balanceInfo->available_balance += $amount;
            $balanceInfo->save();

            $this->sendNotifications($user, $buyLoad, $balanceInfo->available_balance);
            DB::commit();

            $this->logHistory($userId, $refNo, $currentDate, $productCode, $recipientMobileNumber);
            return $this->createLoadTopupResponse($buyLoad);
        } catch (Exception $e) {
            DB::rollBack();

            if ($updateReferenceCounter === true)
                $this->referenceNumberService->generate(ReferenceNumberTypes::BuyLoad);

            throw $e;
        }
    }

    public function processPending(string $userId): array
    {
        $user = $this->users->getUser($userId);
        $balanceInfo = $user->balanceInfo;
        $pendingBuyLoads = $this->buyLoads->getPending($userId);
        $successCount = 0;
        $failCount = 0;

        foreach ($pendingBuyLoads as $buyLoad) {
            $response = $this->atmService->checkStatus($buyLoad->reference_number);
            $buyLoad = $this->handleStatusResponse($buyLoad, $response);
            $amount = $buyLoad->total_amount;

            if ($buyLoad->status === TransactionStatuses::success) {
                if ($balanceInfo->pending_balance > 0) $balanceInfo->pending_balance -= $amount;
            }

            if ($buyLoad->status === TransactionStatuses::failed) {
                $balanceInfo->available_balance += $amount;
                $balanceInfo->pending_balance -= $amount;
            }

            $balanceInfo->save();
            $this->sendNotifications($user, $buyLoad, $balanceInfo->available_balance);

            if ($buyLoad->status === TransactionStatuses::success) $successCount++;
            if ($buyLoad->status === TransactionStatuses::failed) $failCount++;
        }

        return [
            'total_pending_count' => $pendingBuyLoads->count(),
            'success_count' => $successCount,
            'failed_count' => $failCount
        ];
    }


    private function handleLoadTopupResponse(OutBuyLoad $buyLoad, Response $response): OutBuyLoad
    {
        if (!$response->successful()) {
            $errors = $response->json();
            Log::error('BuyLoad UBP Error', $errors);
            $this->transactionFailed();
        } else {
            $responseData = $response->json();
            $state = $responseData['responseCode'];
            $data = $responseData['data'];

            if ($state === AtmPrepaidResponseCodes::requestReceived) {
                $status = TransactionStatuses::pending;
            } elseif ($state === AtmPrepaidResponseCodes::transactionSuccessful) {
                $status = TransactionStatuses::success;
            } else {
                Log::error('Buy Load Transaction Failed', $responseData);
                $status = TransactionStatuses::failed;
                $this->handleErrorResponse($state);
            }

            $buyLoad->status = $status;
            $buyLoad->provider_transaction_id = $data['transactionNo'];
            $buyLoad->user_updated = $buyLoad->user_account_id;
            $buyLoad->transaction_response = json_encode($responseData);
            $buyLoad->save();

            if ($status === TransactionStatuses::success) {
                $this->transactionHistories->log($buyLoad->user_account_id,
                    $buyLoad->transaction_category_id, $buyLoad->id, $buyLoad->reference_number,
                    $buyLoad->total_amount, $buyLoad->transaction_date, $buyLoad->user_account_id);
            }

            return $buyLoad;
        }

    }

    private function handleStatusResponse(OutBuyLoad $buyLoad, Response $response)
    {
        if (!$response->successful()) {
            return;
        } else {
            $responseData = $response->json();
            $state = $responseData['responseCode'];

            if ($state === AtmPrepaidResponseCodes::transactionQueued) return $buyLoad;

            if ($state === AtmPrepaidResponseCodes::transactionSuccessful)
                $status = TransactionStatuses::success;

            if ($state === AtmPrepaidResponseCodes::transactionFailed)
                $status = TransactionStatuses::failed;

            $buyLoad->status = $status;
            $buyLoad->user_updated = $buyLoad->user_account_id;
            $buyLoad->transaction_response = json_encode($responseData);
            $buyLoad->save();

            if ($status === TransactionStatuses::success) {
                $this->transactionHistories->log($buyLoad->user_account_id,
                    $buyLoad->transaction_category_id, $buyLoad->id, $buyLoad->reference_number,
                    $buyLoad->total_amount, $buyLoad->transaction_date, $buyLoad->user_account_id);
            }
        }

        return $buyLoad;
    }

    private function handleErrorResponse(int $state)
    {
        if ($state === AtmPrepaidResponseCodes::invalidMobileNumber)
            $this->invalidMobileNumber();

        if ($state === AtmPrepaidResponseCodes::invalidProductcode)
            $this->invalidProductCode();

        if ($state === AtmPrepaidResponseCodes::insufficientBalance)
            $this->insufficientFunds();

        if ($state === AtmPrepaidResponseCodes::telcoUnavailable)
            $this->telcoUnavailable();

        if ($state === AtmPrepaidResponseCodes::productMismatch)
            $this->productMismatch();

        $this->transactionFailed();
    }

    private function sendNotifications(UserAccount $user, OutBuyLoad $buyLoad, float $availableBalance)
    {
        $usernameField = $this->getUsernameFieldByAvailability($user);
        $username = $this->getUsernameByField($user, $usernameField);
        $notifService = $usernameField === UsernameTypes::Email ? $this->emailService : $this->smsService;

        if ($buyLoad->status === TransactionStatuses::success) {
            $notifService->buyLoadNotification($username, $buyLoad->total_amount, $buyLoad->product_name,
                $buyLoad->recipient_mobile_number, $buyLoad->transaction_date, $availableBalance,
                $buyLoad->reference_number);
        }
    }

    private function createLoadTopupResponse(OutBuyLoad $buyLoad): array
    {
        return [
            'recipient_mobile_number' => $buyLoad->recipient_mobile_number,
            'product_code' => $buyLoad->product_code,
            'product_name' => $buyLoad->product_name,
            'amount' => $buyLoad->total_amount,
            'transaction_number' => $buyLoad->reference_number,
            'transaction_date' => $buyLoad->transaction_date,
        ];
    }

    private function logHistory(string $userId, string $refNo, Carbon $logDate, string $productCode, string $mobileNumber)
    {
        $spModule = SquidPayModuleTypes::BuyLoad;
        $operation = TransactionCategories::BuyLoad;

        $remarks = "Loaded $productCode to mobile number: $mobileNumber.";

        $this->logHistoryService->logUserHistory($userId, $refNo, $spModule,
            null, $logDate, $remarks, $operation);
    }
}
