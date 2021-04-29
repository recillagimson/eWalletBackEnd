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

    public function getByUser(string $userId)
    {
        return $this->model->where('user_account_id', $userId)->first();
    }

}
