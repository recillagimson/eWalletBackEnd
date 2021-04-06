<?php

namespace App\Repositories\AddMoney;

use App\Models\AddMoneyWebBank;
use App\Repositories\Repository;

class WebBankRepository extends Repository implements IWebBankRepository
{
    public function __construct(AddMoneyWebBank $model) {
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
}