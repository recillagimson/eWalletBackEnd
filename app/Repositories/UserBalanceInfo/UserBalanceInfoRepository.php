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

    public function getByUserAccountID(string $userAccountID)
    {
        return $this->model->where('user_account_id', $userAccountID)->first();
    }
    
    public function getUserBalance(string $userID)
    {
        return  $this->model->where('user_account_id', '=', $userID)->pluck('available_balance')->first();
    }

    public function getUserPendingBalance(string $userID)
    {
        return  $this->model->where('user_account_id', '=', $userID)->pluck('pending_balance')->first();
    } 

    public function updateUserBalance(string $userID, float $newBalance){
       return $this->model->where('user_account_id', $userID)->update(['available_balance' => $newBalance]);
    }

    public function updateUserPendingBalance(string $userID, float $newBalance)
    {
        return $this->model->where('user_account_id', $userID)->update(['pending_balance' => $newBalance]);
    }

}
