<?php

namespace App\Services\AddMoney\Providers;

use App\Models\UserAccount;

interface IAddMoneyService
{
    public function addMoney(UserAccount $user, array $request);
    public function cancelAddMoney(UserAccount $user, array $referenceNumber);
}