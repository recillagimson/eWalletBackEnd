<?php

namespace App\Services\AddMoney\Providers;

use App\Models\UserAccount;

interface IAddMoneyService
{
    public function addMoney(UserAccount $user, array $urlParams): array;

    public function cancelAddMoney(UserAccount $user, array $referenceNumber);

    public function getStatus(UserAccount $user, array $request);

    public function updateUserTransactionStatus(UserAccount $user);
}
