<?php

namespace App\Repositories\UserBalanceInfo;

use App\Models\UserBalanceInfo;
use App\Repositories\IRepository;

interface IUserBalanceInfoRepository extends IRepository
{
    public function getByUserAccountID(string $userAccountID);
}
