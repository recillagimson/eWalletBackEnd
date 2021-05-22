<?php

namespace App\Repositories\InReceiveMoney;

use App\Repositories\Repository;
use App\Models\InReceiveMoney;

class InReceiveMoneyRepository extends Repository implements IInReceiveMoneyRepository
{
    public function __construct(InReceiveMoney $model)
    {
        parent::__construct($model);
    }

    public function getSumOfTransactions($from, $to) {
        return $this->model->where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to)
            ->where('status', '!=', 'FAILED')
            ->sum('amount');
    }
}
