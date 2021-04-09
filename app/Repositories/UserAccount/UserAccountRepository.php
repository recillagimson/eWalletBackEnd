<?php

namespace App\Repositories\UserAccount;

use App\Repositories\Repository;
use App\Models\UserAccount;

class UserAccountRepository extends Repository implements IUserAccountRepository
{
    public function __construct(UserAccount $model)
    {
        parent::__construct($model);
    }

    public function getByUsername(string $usernameField, string $username)
    {
        return $this->model->where($usernameField, '=', $username)->first();
    }
}
