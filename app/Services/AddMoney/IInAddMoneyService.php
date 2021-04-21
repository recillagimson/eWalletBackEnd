<?php

namespace App\Services\AddMoney;

use App\Models\UserAccount;

interface IInAddMoneyService
{
    public function addMoney(UserAccount $user, array $request);
    public function cancelAddMoney(UserAccount $user, array $referenceNumber);
}