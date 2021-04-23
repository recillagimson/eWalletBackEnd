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

    public function getUserBalanceInfoById(string $userAccountId) {
        $balance = $this->model->where('user_account_id', $userAccountId)
        ->orderBy('created_at', 'DESC')
        ->first();

        if($balance) {
            return $balance->available_balance;
        }
        // Return 0 if no user balance info
        return 0;
    }

}
