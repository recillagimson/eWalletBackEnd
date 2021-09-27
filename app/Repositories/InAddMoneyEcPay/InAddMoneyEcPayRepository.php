<?php

namespace App\Repositories\InAddMoneyEcPay;

use App\Models\InAddMoneyEcPay;
use App\Repositories\Repository;

class InAddMoneyEcPayRepository extends Repository implements IInAddMoneyEcPayRepository
{
    public function __construct(InAddMoneyEcPay $model)
    {
        parent::__construct($model);
    }

    public function getDataByReferenceNumber(string $referenceNumber) {
        return $this->model->where('reference_number', '=', $referenceNumber)->first();
    }
}
