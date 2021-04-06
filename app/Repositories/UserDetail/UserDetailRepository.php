<?php

namespace App\Repositories\UserDetail;

use App\Repositories\Repository;
use App\Models\UserDetail;

class UserDetailRepository extends Repository implements IUserDetailRepository
{
    public function __construct(UserDetail $model)
    {
        parent::__construct($model);
    }

    public function getByUserAccountID(string $userAccountID)
    {
        return $this->model->where('user_account_id', '=', $userAccountID)->first();
    }
}
