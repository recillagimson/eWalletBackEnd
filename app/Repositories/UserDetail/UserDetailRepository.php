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
}
