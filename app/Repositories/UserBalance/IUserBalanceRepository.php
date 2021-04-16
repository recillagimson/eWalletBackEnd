<?php

namespace App\Repositories\UserBalance;

use App\Repositories\IRepository;

interface IUserBalanceRepository extends IRepository
{
    public function getUserBalance(string $userAccountID);
    public function updateUserBalance(string $UserID, decimal $newUserBalance);
}
