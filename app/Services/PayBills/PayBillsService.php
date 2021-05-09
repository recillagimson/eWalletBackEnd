<?php

namespace App\Services\PayBills;

use App\Models\UserAccount;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Traits\Errors\WithTpaErrors;
use Illuminate\Http\JsonResponse;
use Response;

class PayBillsService implements IPayBillsService
{
    use WithTpaErrors;

    private IOutPayBillsRepository $outPayBills;
    private IBayadCenterService $bayadCenterService;

    public function __construct(IOutPayBillsRepository $outPayBills, IBayadCenterService $bayadCenterService){
        $this->outPayBills = $outPayBills;
        $this->bayadCenterService = $bayadCenterService;
    }

    public function getBillers(): array
    {
        $response = $this->bayadCenterService->getBillers();
        if (!$response->successful()) $this->tpaErrorOccured('Bayad Center');
        return (array)json_decode($response->body());
    }

    public function getBillerInformation(string $billerCode): array
    {
        $response = $this->bayadCenterService->getBillerInformation($billerCode);
        if (!$response->successful()) $this->tpaErrorOccured('Bayad Center');
        return (array)json_decode($response->body());
    }

    public function getOtherCharges(string $billerCode): array
    {
        $response = $this->bayadCenterService->getOtherCharges($billerCode);
        if (!$response->successful()) $this->tpaErrorOccured('Bayad Center');
        return (array)json_decode($response->body());
    }
    

    public function createPayment(UserAccount $user)
    {
        return $this->outPayBills->create([
            'user_account_id' => $user->id,
            'account_number' => '123455667',
            'reference_number' => 'PB0002',
            'amount' => '1400.45',
            'service_fee' => '0.00',
            'total_amount' => '1400.45',
            'transction_category_id' => 'c5b62dbd-95a0-11eb-8473-1c1b0d14e211',
            'transaction_remarks' => 'user pay the bills',
            'email_or_mobile' => 'I do not know',
            'message' => 'random message',
            'status' => '1',
            'billers_code' => 'MER',
            'billers_name' => 'Meralco',
            'bayad_reference_number' => '1231TWE234213',
            'user_created' => 'user_account_id',
            'user_updated' => ''
        ]);
    }
    
    
}
