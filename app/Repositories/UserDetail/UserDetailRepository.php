<?php

namespace App\Repositories\UserDetail;

use App\Models\UserDetail;
use App\Repositories\Repository;

class UserDetailRepository extends Repository implements IUserDetailRepository
{
    public function __construct(UserDetail $model)
    {
        parent::__construct($model);
    }

    public function getByUserAccountId(string $userAccountId)
    {
        return $this->model->where('user_account_id', '=', $userAccountId)->first();
    }
}
