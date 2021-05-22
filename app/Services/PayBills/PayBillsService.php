<?php

namespace App\Services\PayBills;

use App\Enums\ReferenceNumberTypes;
use App\Models\UserAccount;
use App\Models\UserDetail;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithPayBillsErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Transactions\PayBillsHelpers;
use Illuminate\Http\JsonResponse;
use Response;

class PayBillsService implements IPayBillsService
{
    use WithTpaErrors, PayBillsHelpers, WithPayBillsErrors;


    private IOutPayBillsRepository $outPayBills;
    private IBayadCenterService $bayadCenterService;
    private IUserDetailRepository $userDetailRepository;
    private IReferenceNumberService $referenceNumberService;
    private IUserBalanceInfoRepository $userBalanceInfo;

    public function __construct(IOutPayBillsRepository $outPayBills, IBayadCenterService $bayadCenterService, IUserDetailRepository $userDetailRepository, IReferenceNumberService $referenceNumberService, IUserBalanceInfoRepository $userBalanceInfo){
        $this->outPayBills = $outPayBills;
        $this->bayadCenterService = $bayadCenterService;
        $this->userDetailRepository = $userDetailRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->userBalanceInfo = $userBalanceInfo;
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
        $response = $this->bayadCenterService->validateAccount($billerCode, $accountNumber, $data);
        $this->validateTransaction($billerCode, $data, $user);
        return $this->validationResponse($response, $billerCode);
    }


    public function createPayment(string $billerCode, array $data, UserAccount $user): array
    {
        $response = $this->bayadCenterService->createPayment($billerCode, $data, $user);
        return (array)json_decode($response);
    }


    public function inquirePayment(string $billerCode, string $clientReference): array
    {
        $response = $this->bayadCenterService->inquirePayment($billerCode, $clientReference);
        return (array)json_decode($response->body());
    }

    
    
}
