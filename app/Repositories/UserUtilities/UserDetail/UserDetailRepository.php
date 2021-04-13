<?php

namespace App\Repositories\UserUtilities\UserDetail;

use App\Models\UserUtilities\UserDetail;
use App\Repositories\Repository;

class UserDetailRepository extends Repository implements IUserDetailRepository
{
    public function __construct(UserDetail $model)
    {
        parent::__construct($model);
    }

    public function getByUserId(string $userId)
    {
        return $this->model->where('user_account_id', '=', $userId)->first();
    }
}
