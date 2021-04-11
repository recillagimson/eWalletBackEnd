<?php

namespace App\Services\AddMoney\DragonPay;

use App\Models\UserAccount;

interface IWebBankingService
{
    public function generateRequestURL(UserAccount $user, array $urlParams);
    public function getAddMoneyTransStatus(array $identifier);
    public function cancelAddMoney(UserAccount $user, array $referenceNumber);
}