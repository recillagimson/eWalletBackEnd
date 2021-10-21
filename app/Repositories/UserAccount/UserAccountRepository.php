<?php

namespace App\Repositories\UserAccount;

use App\Models\UserAccount;
use App\Repositories\Repository;
use App\Traits\Errors\WithUserErrors;
use Carbon\Carbon;
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

    public function getAllUsersPaginated($attributes, $perPage)
    {
        $result = $this->model;

        if (isset($attributes['filter_by']) && isset($attributes['filter_value'])) {
            $filter_by = $attributes['filter_by'];
            $filter_value = $attributes['filter_value'];
            // IF CUSTOMER NAME
            if ($filter_by === 'CUSTOMER_NAME') {
                $result = $result->whereHas('userDetail', function ($query) use ($filter_value) {
                    $query->whereRaw("concat(first_name, ' ', middle_name, ' ', last_name)LIKE '%$filter_value%'");
                });
            } // IF CUSTOMER ID
            else if ($filter_by === 'CUSTOMER_ID') {
                $result = $result->where('account_number', $filter_value);
            } // IF TIER
            else if ($filter_by === 'TIER') {
                $result = $result->whereHas('tier', function ($query) use ($filter_value) {
                    $query->where('name', $filter_value);
                });
            } // IF STATUS
            else if ($filter_by === 'STATUS') {
                $result = $result->where('status', $filter_value);
            }
        }

        if (isset($from) && isset($to)) {
            $result = $result->whereBetween('created_at', [$attributes['from'], $attributes['to']]);
        }

        return $result->with(['profile', 'tier', 'tierApprovals' => function ($q) {
            return $q->where('status', '!=', 'DECLINED');
        }])->orderBy('created_at', 'DESC')->paginate($perPage);
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
        $record = $this->getAdminUserBaseQuery()->whereHas('profile', function (Builder $query) use ($lastName, $firstName) {
            $query->where([
                ['last_name', 'like', $lastName . '%'],
                ['first_name', 'like', $firstName . '%'],
            ]);
        })->get();

        if($record) {
            return $record;
        }

        return $this->userAccountNotFound();
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

    public function getUserByAccountNumberWithRelations(string $accountNumber)
    {
        return $this->model
            ->with(['profile', 'user_balance_info'])
            ->where(['account_number' => $accountNumber])
            ->first();
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
        return $this->model->with(['merchant_account', 'profile', 'balanceInfo', 'tier']);
    }

    private function getAdminUserBaseQuery(): Builder
    {
        return $this->getBaseQuery()->where('is_admin', '=', true);
    }

    public function getUserAccountByRSBSANo(string $RSBSANo) {
        $record = $this->model->with(['profile', 'user_balance_info'])
            // ->where('account_number', $accountNumber)
            ->where('rsbsa_number', $RSBSANo)
            ->first();

        if($record) {
            return $record;
        }

        return $this->userAccountNotFound();
    }

    public function getUserCount()
    {
        return $this->model->where('created_at','<=',Carbon::now()->subDay())->where('is_active','=',1)->count('*');
    }

    public function getUserByRSBAWithRelations(string $RSBSANo) {
        $record = $this->model->with(['profile', 'user_balance_info'])->where('rsbsa_number', $RSBSANo)->first();

        if($record) {
            return $record;
        }

        $this->userAccountNotFound();
    }

    public function getAccountDetailByRSBSANumber(string $rsbsa_number) {
        return $this->model->where('rsbsa_number', $rsbsa_number)->first();
    }

    public function getUserAccountByRSBSANoV2(string $RSBSANo) {
        $record = $this->model->with(['profile', 'user_balance_info'])
            // ->where('account_number', $accountNumber)
            ->where('rsbsa_number', $RSBSANo)
            ->first();

        if($record) {
            return $record;
        }

        return null;
    }

    public function getAccountByMobileNumber(string $mobileNumber) {
        return $this->model->with(['profile'])->where('mobile_number', $mobileNumber)->first();
    }

    public function getAccountsWithRSBSANumberCount() {
        return $this->model->where('rsbsa_number', '!=', '')->count();
    }
}
