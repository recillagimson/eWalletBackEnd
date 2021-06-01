<?php

namespace App\Services\PayBills;

use Exception;
use App\Models\UserDetail;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;

use App\Models\UserAccount;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\Transaction\ITransactionValidationService;
use App\Traits\Errors\WithPayBillsErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Transactions\PayBillsHelpers;
use Illuminate\Validation\ValidationException;

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

    public function __construct(IOutPayBillsRepository $outPayBills, IBayadCenterService $bayadCenterService, IUserDetailRepository $userDetailRepository, IReferenceNumberService $referenceNumberService, IUserBalanceInfoRepository $userBalanceInfo, IServiceFeeRepository $serviceFeeRepository, ITransactionValidationService $transactionValidationService){
        $this->outPayBills = $outPayBills;
        $this->bayadCenterService = $bayadCenterService;
        $this->userDetailRepository = $userDetailRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->serviceFeeRepository = $serviceFeeRepository;
        $this->transactionValidationService = $transactionValidationService;
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
                $arrayResponse['data'][$x]['code'] == 'CNVRG' || 
                $arrayResponse['data'][$x]['code'] == 'PLDT6' || 
                $arrayResponse['data'][$x]['code'] == 'AEON1' || 
                $arrayResponse['data'][$x]['code'] == 'BNECO' || 
                $arrayResponse['data'][$x]['code'] == 'PRULI' || 
                $arrayResponse['data'][$x]['code'] == 'AECOR' || 
                $arrayResponse['data'][$x]['code'] == 'LAZAE' || 
                $arrayResponse['data'][$x]['code'] == 'SMART' || 
                $arrayResponse['data'][$x]['code'] == 'SSS01' ||
                $arrayResponse['data'][$x]['code'] == 'SSS02' || 
                $arrayResponse['data'][$x]['code'] == 'SSS03' ||
                $arrayResponse['data'][$x]['code'] == 'DFA01' || 
                $arrayResponse['data'][$x]['code'] == 'POEA1'
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
        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) SEND MONEY
        $this->transactionValidationService->checkUserMonthlyTransactionLimit($user, $data['amount'], TransactionCategoryIds::payBills);
        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) SEND MONEY

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
        return (array)json_decode($outPayBills);
    }


    public function inquirePayment(string $billerCode, string $clientReference): array
    {
        $response = $this->bayadCenterService->inquirePayment($billerCode, $clientReference);
        $arrayResponse = (array)json_decode($response->body(), true);
        if (isset($arrayResponse['exception'])) return $this->tpaErrorCatch($arrayResponse);
        return $arrayResponse;
    }

    
    
}
