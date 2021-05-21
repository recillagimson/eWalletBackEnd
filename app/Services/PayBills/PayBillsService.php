<?php

namespace App\Services\PayBills;

use App\Enums\ReferenceNumberTypes;
use App\Models\UserAccount;
use App\Models\UserDetail;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Transactions\PayBillsHelpers;
use Illuminate\Http\JsonResponse;
use Response;

class PayBillsService implements IPayBillsService
{
    use WithTpaErrors, PayBillsHelpers;

    private IOutPayBillsRepository $outPayBills;
    private IBayadCenterService $bayadCenterService;
    private IUserDetailRepository $userDetailRepository;
    private IReferenceNumberService $referenceNumberService;

    public function __construct(IOutPayBillsRepository $outPayBills, IBayadCenterService $bayadCenterService, IUserDetailRepository $userDetailRepository, IReferenceNumberService $referenceNumberService){
        $this->outPayBills = $outPayBills;
        $this->bayadCenterService = $bayadCenterService;
        $this->userDetailRepository = $userDetailRepository;
        $this->referenceNumberService = $referenceNumberService;
    }

    
    public function getBillers(): array
    {
        $response = $this->bayadCenterService->getBillers();
        if (!$response->successful()) $this->tpaErrorCatch($response);
        return (array)json_decode($response->body());
    }


    public function getBillerInformation(string $billerCode): array
    {
        $response = $this->bayadCenterService->getBillerInformation($billerCode);
        if (!$response->successful()) $this->tpaErrorCatch($response);
        return (array)json_decode($response->body());
    }


    public function getWalletBalance(): array
    {
        $response = $this->bayadCenterService->getWalletBalance();
        if (!$response->successful()) $this->tpaErrorCatch($response);
        return (array)json_decode($response->body());
    }


    public function verifyAccount(string $billerCode, string $accountNumber, $data): array
    {
        $response = $this->bayadCenterService->verifyAccount($billerCode, $accountNumber, $data);
        if (!$response->successful()) $this->tpaErrorCatch($response);
        return (array)json_decode($response->body());
    }


    public function createPayment(string $billerCode, array $data, UserAccount $user): array
    {
        $response = $this->bayadCenterService->createPayment($billerCode, $data, $user);

       // $this->saveTransaction($user, $billerCode, json_decode($response, true));
        return (array)json_decode($response);
    }


    public function inquirePayment(string $billerCode, string $clientReference): array
    {
        $response = $this->bayadCenterService->inquirePayment($billerCode, $clientReference);
        if (!$response->successful()) $this->tpaErrorCatch($response);
        return (array)json_decode($response->body());
    }

    
    
}
