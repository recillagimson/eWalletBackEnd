<?php

namespace App\Repositories\UserAccount;

use App\Models\UserAccount;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Builder;

class UserAccountRepository extends Repository implements IUserAccountRepository
{
    public function __construct(UserAccount $model)
    {
        parent::__construct($model);
    }

    public function getUser(string $id)
    {
        return $this->getBaseQuery()->where('id', '=', $id)->first();
    }

    public function getByUsername(string $usernameField, string $username)
    {
        return $this->model->where($usernameField, '=', $username)->first();
    }

    private function getBaseQuery(): Builder
    {
        return $this->model->with(['profile', 'balanceInfo','tier']);
    }

    public function getUserInfo(string $userAccountID)
    {
        return $this->getBaseQuery()->where('id', '=', $userAccountID)->first();
    }
}
