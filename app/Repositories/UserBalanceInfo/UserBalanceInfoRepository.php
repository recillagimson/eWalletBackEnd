<?php

namespace App\Repositories\UserBalanceInfo;

use App\Repositories\Repository;
use App\Models\UserBalanceInfo;

class UserBalanceInfoRepository extends Repository implements IUserBalanceInfoRepository
{
    public function __construct(UserBalanceInfo $model)
    {
        parent::__construct($model);
    }

    public function getUserBalance(string $userID)
    {
        return  $this->model->where('user_account_id', '=', $userID)->pluck('available_balance')->first();
    }       

    public function updateUserBalance(string $userID, float $newBalance){
       return $this->model->where('user_account_id', $userID)->update(['available_balance' => $newBalance]);
    }

}
