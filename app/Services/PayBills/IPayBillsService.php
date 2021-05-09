<?php

namespace App\Services\PayBills;

use App\Models\UserAccount;

/**
 * @property 
 * @property 
 *
 */
interface IPayBillsService
{
    public function getBillers();
    public function createPayment(UserAccount $user);
    public function getBillerInformation(string $billerCode);
    public function getOtherCharges(string $billerCode);
}
