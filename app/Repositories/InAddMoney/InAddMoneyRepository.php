<?php

namespace App\Repositories\InAddMoney;

use App\Enums\DragonPayStatusTypes;
use App\Models\InAddMoneyFromBank;
use App\Repositories\Repository;

class InAddMoneyRepository extends Repository implements IInAddMoneyRepository
{
    public function __construct(InAddMoneyFromBank $model) {
        parent::__construct($model);
    }

    public function getLastByReferenceNumber()
    {
        return $this->model->orderBy('reference_number', 'desc')->first();
    }

    public function getByReferenceNumber(string $referenceNumber)
    {
        return $this->model->where('reference_number', $referenceNumber)->first();
    }

    public function getLatestPendingByUserAccountID(string $userAccountID)
    {
        return $this->model->where('user_account_id', $userAccountID)
                            ->where('status', DragonPayStatusTypes::Pending)
                            ->where('deleted_at', null)
                            ->orderBy('created_at', 'desc')
                            ->first();
    }

    public function getByMultipleReferenceNumber(array $referenceNumbers)
    {
        return $this->model->whereIn('reference_number', $referenceNumbers)->get();
    }

    public function getByUserAccountID(string $userAccountID)
    {
        return $this->model->where('user_account_id', $userAccountID)->orderBy('created_at', 'asc')->get();
    }

    public function getByUserAccountIDAndStatus(string $userAccountID, $status)
    {
        return $this->model->where('user_account_id', $userAccountID)->where('status', $status)->get();
    }

    public function getByUserAccountIDBetweenDates(string $userAccountID, string $startDate, string $endDate)
    {
        return $this->model->where('user_account_id', $userAccountID)->whereBetween('created_at', [$startDate, $endDate])->get();
    }
}