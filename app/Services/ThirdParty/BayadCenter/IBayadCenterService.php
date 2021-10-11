<?php

namespace App\Services\ThirdParty\BayadCenter;

use App\Models\UserAccount;
use Illuminate\Http\Client\Response;

interface IBayadCenterService
{
    public function getBillers();
    public function getBillerInformation(string $billerCode);
    public function getOtherCharges(string $billerCode);
    public function getOtherChargesMECOP(string $billerCode, $data);
    public function validateAccount(string $billerCode, string $accountNumber, $data);
    public function createPayment(string $billerCode, array $data, UserAccount $user);
    public function inquirePayment(string $billerCode, string $clientReference);
    public function getWalletBalance();
}
