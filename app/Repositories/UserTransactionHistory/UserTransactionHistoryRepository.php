<?php

namespace App\Repositories\UserTransactionHistory;

use App\Models\UserBalanceInfo;
use App\Repositories\Repository;

class UserTransactionHistoryRepository extends Repository implements IUserTransactionHistoryRepository
{
    public function __construct(UserBalanceInfo $model)
    {
        parent::__construct($model);
    }
}
