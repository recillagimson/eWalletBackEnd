<?php

namespace App\Repositories\UserAccount;

use App\Models\UserAccount;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserAccountRepository extends Repository implements IUserAccountRepository
{
    public function __construct(UserAccount $model)
    {
        parent::__construct($model);
    }

    public function getAdminUsers(): Collection
    {
        return $this->getAdminUserBaseQuery()->get();
    }

    public function getAdminUser(string $id)
    {
        return $this->getAdminUserBaseQuery()->where('id', '=', $id)->first();
    }

    public function getAdminUsersByEmail(string $email): Collection
    {
        return $this->getAdminUserBaseQuery()->where('email', '=', $email)->get();
    }

    public function getAdminUsersByName(string $lastName, string $firstName): Collection
    {
        return $this->getAdminUserBaseQuery()->whereHas('profile', function (Builder $query) use ($lastName, $firstName) {
            $query->where([
                ['last_name', 'like', $lastName . '%'],
                ['first_name', 'like', $firstName . '%'],
            ]);
        })->get();
    }

    public function getUser(string $id)
    {
        return $this->getBaseQuery()->where('id', '=', $id)->first();
    }

    public function getByUsername(string $usernameField, string $username)
    {
        return $this->model->where($usernameField, '=', $username)->first();
    }

    public function getUserInfo(string $userAccountID)
    {
        return $this->getUserDetailsBaseQuery()->where('id', '=', $userAccountID)->first();
    }

    public function getByEmail(string $emailField, string $email)
    {
        return $this->model->where($emailField, '=', $email)->first();
    }

    private function getBaseQuery(): Builder
    {
        return $this->model->with(['profile', 'balanceInfo']);
    }

    private function getUserDetailsBaseQuery(): Builder
    {
        return $this->model->with(['profile', 'balanceInfo', 'tier']);
    }

    private function getAdminUserBaseQuery(): Builder
    {
        return $this->getBaseQuery()->where('is_admin', '=', true);
    }



}
