<?php

namespace App\Services\PayBills;

use Exception;
use App\Models\UserDetail;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Models\UserAccount;
//use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Traits\Errors\WithPayBillsErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Transactions\PayBillsHelpers;
use Illuminate\Validation\ValidationException;
use App\Services\Utilities\CSV\ICSVService;
use Carbon\Carbon;

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
    //private ILogHistoryRepository $logHistory;

    public function __construct(IOutPayBillsRepository $outPayBills, IBayadCenterService $bayadCenterService, IUserDetailRepository $userDetailRepository, IReferenceNumberService $referenceNumberService, IUserBalanceInfoRepository $userBalanceInfo, IServiceFeeRepository $serviceFeeRepository, ITransactionValidationService $transactionValidationService, IUserAccountRepository $userAccountRepository, IOutPayBillsRepository $outPayBillsRepository, IUserTransactionHistoryRepository $transactionHistories, INotificationService $notificationService,
                                ICSVService $csvService){
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
       // $this->logHistory = $logHistory;
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
            if( 
                $arrayResponse['data'][$x]['code'] == 'MECOR' || 
                $arrayResponse['data'][$x]['code'] == 'MWCOM' || 
                $arrayResponse['data'][$x]['code'] == 'MWSIN' || 
                $arrayResponse['data'][$x]['code'] == 'RFID1' || 
                $arrayResponse['data'][$x]['code'] == 'ETRIP' || 
                //$arrayResponse['data'][$x]['code'] == 'CNVRG' || 
                //$arrayResponse['data'][$x]['code'] == 'PLDT6' || 
               // $arrayResponse['data'][$x]['code'] == 'AEON1' || 
                //$arrayResponse['data'][$x]['code'] == 'BNECO' || 
                $arrayResponse['data'][$x]['code'] == 'PRULI' || 
                //$arrayResponse['data'][$x]['code'] == 'AECOR' || 
               // $arrayResponse['data'][$x]['code'] == 'LAZAE' || 
                $arrayResponse['data'][$x]['code'] == 'SMART' || 
                //$arrayResponse['data'][$x]['code'] == 'SSS01' ||
               // $arrayResponse['data'][$x]['code'] == 'SSS02' || 
                $arrayResponse['data'][$x]['code'] == 'SSS03' || 
                //$arrayResponse['data'][$x]['code'] == 'DFA01' || 
                //$arrayResponse['data'][$x]['code'] == 'POEA1'
                $arrayResponse['data'][$x]['code'] == 'MBCCC' || 
                $arrayResponse['data'][$x]['code'] == 'BPI00' || 
                $arrayResponse['data'][$x]['code'] == 'BNKRD' || 
                $arrayResponse['data'][$x]['code'] == 'UNBNK' || 
                $arrayResponse['data'][$x]['code'] == 'PILAM' || 
                $arrayResponse['data'][$x]['code'] == 'ADMSN' || 
                $arrayResponse['data'][$x]['code'] == 'UBNK4' || 
                $arrayResponse['data'][$x]['code'] == 'ASLNK' 

                ){
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


    public function validateAccount(string $billerCode, string $accountNumber, $data, UserAccount $user): array
    {
        $response = $this->bayadCenterService->validateAccount($billerCode, $accountNumber, $data);
        $arrayResponse = (array)json_decode($response->body(), true);
        if (isset($arrayResponse['exception'])) return $this->tpaErrorCatch($arrayResponse);
        $this->validateTransaction($billerCode, $data, $user);
        return $this->validationResponse($user, $response, $billerCode, $data);
    }


    public function createPayment(string $billerCode, array $data, UserAccount $user): array
    {
        $response = $this->bayadCenterService->createPayment($billerCode, $data, $user);
        $arrayResponse = (array)json_decode($response->body(), true);
        if (isset($arrayResponse['exception'])) return $this->tpaErrorCatch($arrayResponse);
        $outPayBills = $this->saveTransaction($user, $billerCode, $response);
       
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

    public function downloadListOfBillersCSV()
    {
        $billers = $this->outPayBillsRepository->getAllBillers();
        $columns = array('Customer Account ID', 'Customer Name', 'Reference Number', 'Date of Transaction', 'Biller', 'Amount', 'Status');
        $datas = [];

        foreach ($billers as $biller) {
            array_push($datas, [
                'Customer Account ID'  => $biller->user_account_id,
                'Customer Name' => ucwords($biller->user_detail->first_name) . ' ' . ucwords($biller->user_detail->last_name),
                'Reference Number' => $biller->reference_number,
                'Date of Transaction'  => Carbon::parse($biller->transaction_date)->format('F d, Y g:i A'),
                'Biller'  => $biller->billers_name,
                'Amount'  => $biller->total_amount,
                'Status'  => ($biller->status) ? 'Paid' : 'Not Paid',

            ]);
        }

        return $this->csvService->generateCSV($datas, $columns);
    }
    
}
