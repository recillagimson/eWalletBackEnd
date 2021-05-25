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
        return (array)json_decode($response->body());
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


    public function validateAccount(string $billerCode, string $accountNumber, $data, UserAccount $user): array
    {
        try {

            $response = $this->bayadCenterService->validateAccount($billerCode, $accountNumber, $data);
            $arrayResponse =  (array)json_decode($response);
        
        } catch (Exception $ex) { 
            $this->tpaInvalidBiller();
        }    
        
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
