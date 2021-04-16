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

    public function getUserBalance(string $userAccountID)    
    {
        return $this->model->where('user_account_id', '=', $userAccountID)->pluck('available_balance')->first();
    }

    public function updateUserBalance($UserID, $newUserBalance)
    {
        return $this->model->where('user_account_id','=',$UserID)
        ->update(['available_balance'   =>  $newUserBalance]);
    }
}
