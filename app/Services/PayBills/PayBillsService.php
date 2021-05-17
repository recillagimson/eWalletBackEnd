<?php

namespace App\Services\PayBills;

use App\Models\UserAccount;
use App\Models\UserDetail;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Traits\Errors\WithTpaErrors;
use Illuminate\Http\JsonResponse;
use Response;

class PayBillsService implements IPayBillsService
{
    use WithTpaErrors;

    private IOutPayBillsRepository $outPayBills;
    private IBayadCenterService $bayadCenterService;
    private IUserDetailRepository $userDetailRepository;

    public function __construct(IOutPayBillsRepository $outPayBills, IBayadCenterService $bayadCenterService, IUserDetailRepository $userDetailRepository){
        $this->outPayBills = $outPayBills;
        $this->bayadCenterService = $bayadCenterService;
        $this->userDetailRepository = $userDetailRepository;
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

    public function getRequiredFields(string $billerCode)
    {
        return $this->bayadCenterService->getRequiredFields($billerCode);
    }


    public function getOtherCharges(string $billerCode): array
    {
        $response = $this->bayadCenterService->getOtherCharges($billerCode);
        if (!$response->successful()) $this->tpaErrorCatch($response);
        return (array)json_decode($response->body());
    }


    public function getWalletBalance(): array
    {
        $response = $this->bayadCenterService->getWalletBalance();
        if (!$response->successful()) $this->tpaErrorCatch($response);
        return (array)json_decode($response->body());
    }


    public function verifyAccount(string $billerCode, string $accountNumber, $data)//: array
    {
        $response = $this->bayadCenterService->verifyAccount($billerCode, $accountNumber, $data);
        if (!$response->successful()) $this->tpaErrorCatch($response);
        return (array)json_decode($response->body());
    }


    public function createPayment(string $billerCode, array $data, UserAccount $user)//: array
    {
        $response = $this->bayadCenterService->createPayment($billerCode, $data, $user);
        if (!$response->successful()) $this->tpaErrorCatch($response);
        $response = json_decode($response, true);
        $userDetail = $this->userDetailRepository->getByUserId($user->id);
       
        return (array)json_decode($response);
    }


    public function inquirePayment(string $billerCode, string $clientReference): array
    {
        $response = $this->bayadCenterService->inquirePayment($billerCode, $clientReference);
        if (!$response->successful()) $this->tpaErrorCatch($response);
        return (array)json_decode($response->body());
    }

    
    private function outPayBills(UserDetail $user, string $billerCode, $response)
    {
        return $this->outPayBills->create([
            'user_account_id' => $user->id,
            'account_number' => '123455667',
            'reference_number' => $response['data']['transactionId'],
            'amount' => '1400.45',
            'service_fee' => '0.00',
            'total_amount' => '1400.45',
            'transction_category_id' => 'c5b62dbd-95a0-11eb-8473-1c1b0d14e211',
            'transaction_remarks' => 'user pay the bills',
            'email_or_mobile' => 'I do not know',
            'message' => 'random message',
            'status' => '1',
            'billers_code' => $billerCode,
            'billers_name' => $user->firtst,
            'bayad_reference_number' => $response['data']['referenceNumber'],
            'user_created' => $user->id,
            'user_updated' => ''
        ]);
    }
    
    
}
