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

    public function getSumOfTransactions($from, $to, $userAccountId) {
        return $this->model->where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to)
            ->where('status', '!=', 'FAILED')
            ->where('user_account_id', $userAccountId)
            ->sum('amount');
    }

    public function getTotalReceiveMoney()
    {
        return $this->model->where('transaction_date','<=',Carbon::now()->subDay())->where('status','=','successful')->sum('amount');
    }
}
