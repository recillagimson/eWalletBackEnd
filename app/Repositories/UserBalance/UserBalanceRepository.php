<?php

namespace App\Repositories\UserBalance;

use App\Models\UserBalanceInfo;
use App\Repositories\Repository;

class UserBalanceRepository extends Repository implements IUserBalanceRepository
{
    public function __construct(UserBalanceInfo $model)
    {
        parent::__construct($model);
    }
}