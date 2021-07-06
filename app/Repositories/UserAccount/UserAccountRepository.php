<?php

namespace App\Repositories\UserAccount;

use App\Models\UserAccount;
use App\Repositories\Repository;
use App\Traits\Errors\WithErrors;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserAccountRepository extends Repository implements IUserAccountRepository
{

    use WithUserErrors;

    public function __construct(UserAccount $model)
    {
        parent::__construct($model);
    }

    public function getAdminUsers(): Collection
    {
        return $this->getAdminUserBaseQuery()->get();
    }

    public function getAllUsersPaginated($perPage)
    {
        $result = $this->model->with(['profile', 'tier'])->orderBy('created_at', 'DESC')->paginate($perPage);
        
        return $result;
    }

    public function findById($id)
    {
        $result = $this->model->with(['profile', 'tier'])->find($id);
        
        return $result;
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

    public function getUserByAccountNumber(string $accountNumber)
    {
        return $this->model->where(['account_number' => $accountNumber])->first();
    }

    public function getAccountNumber(string $userID)
    {
        return $this->model->where('id', $userID)->pluck('account_number')->first();
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

    public function getUserAccountByAccountNumberAndRSBSANo(string $accountNumber, string $RSBSANo) {
        $record = $this->model->with(['profile', 'user_balance_info'])
            ->where('account_number', $accountNumber)
            ->where('rsbsa_number', $RSBSANo)
            ->first();
        
        if($record) {
            return $record;
        }

        return $this->userAccountNotFound();
    }

}
