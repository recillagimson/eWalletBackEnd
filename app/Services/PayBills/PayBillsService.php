<?php

namespace App\Services\PayBills;

use App\Enums\ReferenceNumberTypes;
use App\Models\UserAccount;
use App\Models\UserDetail;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithPayBillsErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Transactions\PayBillsHelpers;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PayBillsService implements IPayBillsService
{
    use WithTpaErrors, PayBillsHelpers, WithPayBillsErrors;


    private IOutPayBillsRepository $outPayBills;
    private IBayadCenterService $bayadCenterService;
    private IUserDetailRepository $userDetailRepository;
    private IReferenceNumberService $referenceNumberService;
    private IUserBalanceInfoRepository $userBalanceInfo;
    private IServiceFeeRepository $serviceFeeRepository;

    public function __construct(IOutPayBillsRepository $outPayBills, IBayadCenterService $bayadCenterService, IUserDetailRepository $userDetailRepository, IReferenceNumberService $referenceNumberService, IUserBalanceInfoRepository $userBalanceInfo, IServiceFeeRepository $serviceFeeRepository){
        $this->outPayBills = $outPayBills;
        $this->bayadCenterService = $bayadCenterService;
        $this->userDetailRepository = $userDetailRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->serviceFeeRepository = $serviceFeeRepository;
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
        return (array)json_decode($response->body());
    }


    public function getWalletBalance(): array
    {
        $response = $this->bayadCenterService->getWalletBalance();
        return (array)json_decode($response->body());
    }


    public function validateAccount(string $billerCode, string $accountNumber, $data, UserAccount $user)//: array
    {
        $response = $this->bayadCenterService->validateAccount($billerCode, $accountNumber, $data);
        $arrayResponse = (array)json_decode($response);

        if (isset($arrayResponse['exception'])) return $arrayResponse;

        $this->validateTransaction($billerCode, $data, $user);
        return $this->validationResponse($user, $response, $billerCode, $data);

    }


    public function createPayment(string $billerCode, array $data, UserAccount $user): array
    {
        $response = $this->bayadCenterService->createPayment($billerCode, $data, $user);
        $arrayResponse =  (array)json_decode($response);
        
        if (isset($arrayResponse['exception'])) return $arrayResponse; 

        $outPayBills = $this->saveTransaction($user, $billerCode, $response);
        return (array)json_decode($outPayBills);
    }


    public function inquirePayment(string $billerCode, string $clientReference): array
    {
        $response = $this->bayadCenterService->inquirePayment($billerCode, $clientReference);
        return (array)json_decode($response->body());
    }

    
    
}
