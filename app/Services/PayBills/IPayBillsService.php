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
    public function getBillerInformation(string $billerCode);
    public function getRequiredFields(string $billerCode);
    public function getOtherCharges(string $billerCode);
    public function verifyAccount(string $billerCode, string $accountNumber, $data);
    public function createPayment(string $billerCode, array $data);
    public function inquirePayment(string $billerCode, string $clientReference);
    public function getWalletBalance();
}
