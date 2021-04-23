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
}