<?php

namespace App\Repositories\FundTransfer;

use App\Repositories\Repository;
use App\Models\UserBalanceInfo;

class UserBalanceInfoRepository extends Repository implements IUserBalanceInfoRepository
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
