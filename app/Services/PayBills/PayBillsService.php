<?php

namespace App\Services\PayBills;

use App\Models\UserAccount;
use App\Repositories\OutPayBills\IOutPayBillsRepository;

class PayBillsService implements IPayBillsService
{

    private IOutPayBillsRepository $outPayBills;

    public function __construct(IOutPayBillsRepository $outPayBills){
        $this->outPayBills = $outPayBills;
    }

    public function getBillers($var)
    {
        return $var;
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
