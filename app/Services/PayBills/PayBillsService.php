<?php

namespace App\Services\PayBills;

use App\Enums\PayBillsConfig;
use App\Enums\TransactionStatuses;
use App\Models\UserAccount;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\CSV\ICSVService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithPayBillsErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Transactions\PayBillsHelpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayBillsService implements IPayBillsService
{
    use WithTpaErrors, PayBillsHelpers, WithPayBillsErrors;


    private IOutPayBillsRepository $outPayBills;
    private IBayadCenterService $bayadCenterService;
    private IUserDetailRepository $userDetailRepository;
    private IReferenceNumberService $referenceNumberService;
    private IUserBalanceInfoRepository $userBalanceInfo;
    private IServiceFeeRepository $serviceFeeRepository;
    private ITransactionValidationService $transactionValidationService;
    private IUserAccountRepository $userAccountRepository;
    private IOutPayBillsRepository $outPayBillsRepository;
    private IUserTransactionHistoryRepository $transactionHistories;
    private INotificationService $notificationService;
    private ICSVService $csvService;
    private ILogHistoryRepository $logHistory;
    private IEmailService $emailService;
    private ISmsService $smsService;
    private INotificationRepository $notificationRepository;

    public function __construct(IOutPayBillsRepository $outPayBills, IBayadCenterService $bayadCenterService, IUserDetailRepository $userDetailRepository, IReferenceNumberService $referenceNumberService, IUserBalanceInfoRepository $userBalanceInfo, IServiceFeeRepository $serviceFeeRepository, ITransactionValidationService $transactionValidationService, IUserAccountRepository $userAccountRepository, IOutPayBillsRepository $outPayBillsRepository, IUserTransactionHistoryRepository $transactionHistories, INotificationService $notificationService, ICSVService $csvService, ILogHistoryRepository $logHistory, IEmailService $emailService, ISmsService $smsService, INotificationRepository $notificationRepository)
    {
        $this->outPayBills = $outPayBills;
        $this->bayadCenterService = $bayadCenterService;
        $this->userDetailRepository = $userDetailRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->serviceFeeRepository = $serviceFeeRepository;
        $this->transactionValidationService = $transactionValidationService;
        $this->userAccountRepository = $userAccountRepository;
        $this->outPayBillsRepository = $outPayBillsRepository;
        $this->transactionHistories = $transactionHistories;
        $this->notificationService = $notificationService;
        $this->csvService = $csvService;
        $this->logHistory = $logHistory;
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        $this->notificationRepository = $notificationRepository;
    }


    public function getBillers(): array
    {
        $response = $this->bayadCenterService->getBillers();
        $arrayResponse = (array)json_decode($response->body(), true);
        $billersCount = count($arrayResponse['data']);
        $newResponse = array();
        $active = array('active' => '1');
        $inActive = array('active' => '0');

        //list of active billing partners
        for ($x = 0; $x < $billersCount; $x++) {
            if (in_array($arrayResponse['data'][$x]['code'], PayBillsConfig::billerCodes)) {
                $newResponse['data'][$x] = array_merge($arrayResponse['data'][$x], $active);
            } else {
                $newResponse['data'][$x] = array_merge($arrayResponse['data'][$x], $inActive);
            }
        }

        return $newResponse;
    }


    public function getBillerInformation(string $billerCode): array
    {
        $response = $this->bayadCenterService->getBillerInformation($billerCode);
        $arrayResponse = (array)json_decode($response->body(), true);
        if (isset($arrayResponse['exception'])) return $this->tpaErrorCatch($arrayResponse);
        return $arrayResponse;
    }


    public function getWalletBalance(): array
    {
        $response = $this->bayadCenterService->getWalletBalance();
        $arrayResponse = (array)json_decode($response->body(), true);
        if (isset($arrayResponse['exception'])) return $this->tpaErrorCatch($arrayResponse);
        return $arrayResponse;
    }


    // old function
    public function oldValidateAccount(string $billerCode, string $accountNumber, $data, UserAccount $user): array
    {
        $this->firstLayerValidation($billerCode, $accountNumber, $data);

        $response = $this->bayadCenterService->validateAccount($billerCode, $accountNumber, $data);
        $arrayResponse = (array)json_decode($response->body(), true);

        if (isset($arrayResponse['exception'])) return $this->tpaErrorCatch($arrayResponse);
        if ($arrayResponse['data'] === "NOT_FOUND") return $this->tpaErrorCatch($arrayResponse);
        if (isset($arrayResponse['message']) === "Internal server error") return $this->tpaErrorCatch($arrayResponse);
        if (isset($arrayResponse['data']) === "Internal Server Error") return $this->tpaErrorCatch($arrayResponse);
        if (isset($arrayResponse['data']['code']) && $arrayResponse['data']['code'] === 1) return $this->tpaErrorCatchMeralco($arrayResponse, $this->getServiceFee($user), $this->getOtherCharges($billerCode));
        $this->checkAmountAndMonthlyLimit($billerCode, $data, $user);
        return $this->validationResponse($user, $response, $billerCode, $data);
    }

 
    public function validateAccount(string $billerCode, string $accountNumber, $data, UserAccount $user): array
    {
        $response = $this->bayadCenterService->validateAccount($billerCode, $accountNumber, $data);
        $arrayResponse = (array)json_decode($response->body(), true);

        // To catch the DFO account from MECOR
        if (isset($arrayResponse['data']['code']) && $arrayResponse['data']['code'] === 1) $this->accountWithDFO($arrayResponse, $this->getServiceFee($user), $this->getOtherCharges($billerCode));

        // To catch bayad validation for invalid accounts 
        if (isset($arrayResponse['data']) && in_array($arrayResponse['data'], PayBillsConfig::billerInvalidMsg)) $this->invalidAccountNumber();

        // To catch endpointRequestTimeout
        if(isset($arrayResponse['message']) && $arrayResponse['message'] ===  PayBillsConfig::endpointRequestTimeOut) $this->endpointRequestTimeOut();

        // To catch bayad general Error
        if (isset($arrayResponse['exception'])) $this->catchBayadErrors($arrayResponse, $billerCode, $user);

        $this->checkAmountAndMonthlyLimit($billerCode, $data, $user);
        return $this->validationResponse($user, $response, $billerCode, $data);
    }   


    public function createPayment(string $billerCode, array $data, UserAccount $user): array
    {
        $response = $this->bayadCenterService->createPayment($billerCode, $data, $user);
        $arrayResponse = (array)json_decode($response->body(), true);
        if (isset($arrayResponse['exception'])) return $this->tpaErrorCatch($arrayResponse);
        $outPayBills = $this->saveTransaction($user, $billerCode, $response, $response);

        // For automatic validation of incoming pending status
        $this->processPending($user);

        return (array)json_decode($outPayBills);
    }


    public function inquirePayment(string $billerCode, string $clientReference): array
    {
        $response = $this->bayadCenterService->inquirePayment($billerCode, $clientReference);
        $arrayResponse = (array)json_decode($response->body(), true);
        if (isset($arrayResponse['exception'])) return $this->tpaErrorCatch($arrayResponse);
        return $arrayResponse;
    }


    public function processPending(UserAccount $user): array
    {
        $balanceInfo = $user->balanceInfo;
        $pendingOutPayBills = $this->outPayBillsRepository->getPending($user->id);
        $successCount = 0;
        $failCount = 0;
        $response = array();

        foreach ($pendingOutPayBills as $payBill) {

            $response = $this->bayadCenterService->inquirePayment($payBill->billers_code, $payBill->client_reference);
            $payBill = $this->handleStatusResponse($payBill, $response);
            $amount = $payBill->total_amount;

            if ($payBill->status === TransactionStatuses::success) {
                if ($balanceInfo->pending_balance > 0) $balanceInfo->pending_balance -= $amount;
            }

            if ($payBill->status === TransactionStatuses::failed) {
                $balanceInfo->available_balance += $amount;
                $balanceInfo->pending_balance -= $amount;
            }

            $balanceInfo->save();
          //$this->sendNotifications($user, $payBill, $balanceInfo->available_balance);

            if ($payBill->status === TransactionStatuses::success) $successCount++;
            if ($payBill->status === TransactionStatuses::failed) $failCount++;
        }


        return [
            'total_pending_count' => $pendingOutPayBills->count(),
            'success_count' => $successCount,
            'failed_count' => $failCount
        ];
    }

    public function processAllPending()
    {
        $users = $this->outPayBills->getUsersWithPending();

        foreach ($users as $user) {
            Log::info('Pay Bills Processing User:', ['user_account_id' => $user->user_account_id]);
            $user = $this->userAccountRepository->getUser($user->user_account_id);
            $this->processPending($user);
        }
    }

    public function downloadListOfBillersCSV()
    {
        $billers = $this->outPayBillsRepository->getAllBillers();
        $columns = array('Customer Account ID', 'Customer Name', 'Reference Number', 'Date of Transaction', 'Biller', 'Amount', 'Status');
        $datas = [];

        foreach ($billers as $biller) {
            array_push($datas, [
                'Customer Account ID' => $biller->user_account_id,
                'Customer Name' => ucwords($biller->user_detail->first_name) . ' ' . ucwords($biller->user_detail->last_name),
                'Reference Number' => $biller->reference_number,
                'Date of Transaction' => Carbon::parse($biller->transaction_date)->format('F d, Y g:i A'),
                'Biller' => $biller->billers_name,
                'Amount' => $biller->total_amount,
                'Status' => ($biller->status) ? 'Paid' : 'Not Paid',

            ]);
        }

        return $this->csvService->generateCSV($datas, $columns);
    }

}
