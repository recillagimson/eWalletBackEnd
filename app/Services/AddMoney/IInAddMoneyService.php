<?php

namespace App\Services\AddMoney;

use App\Models\UserAccount;

interface IInAddMoneyService
{
    public function addMoney(UserAccount $user, array $request);
    public function cancelAddMoney(UserAccount $user, array $referenceNumber);
    public function getStatus(UserAccount $user, array $request);
    public function updateUserTransactionStatus(UserAccount $user);
}