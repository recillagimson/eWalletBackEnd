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
}
