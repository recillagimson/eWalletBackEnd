<?php

namespace App\Repositories\OutSendMoney;

use App\Repositories\Repository;
use App\Models\OutSendMoney;

class OutSendMoneyRepository extends Repository implements IOutSendMoneyRepository
{
    public function __construct(OutSendMoney $model)
    {
        parent::__construct($model);
    }

    public function getLastRefNo()
    {
        return $this->model->orderByDesc('reference_number')->pluck('reference_number')->first();
    }

    public function getSumOfTransactions($from, $to) {
        return $this->model->where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to)
            ->where('status', '!=', 'FAILED')
            ->sum('total_amount');
    }

}   
