<?php

namespace App\Repositories\FundTransfer;

use App\Models\UserBalanceInfo;
use App\Repositories\IRepository;

interface IUserBalanceInfoRepository extends IRepository
{
    public function getUserBalance(string $userAccountID);
    public function updateUserBalance(string $UserID, decimal $newUserBalance);
}
