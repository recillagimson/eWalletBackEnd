<?php

namespace App\Repositories\InAddMoneyBPI;

use App\Models\InAddMoneyBPI;
use App\Repositories\Repository;

class InAddMoneyBPIRepository extends Repository implements IInAddMoneyBPIRepository
{
    public function __construct(InAddMoneyBPI $model)
    {
        parent::__construct($model);
    }

    public function getSumOfTransactions(string $from, string $to, string $userId) {
        return $this->model->where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to)
            ->where('status', '!=', 'error')
            ->where('user_account_id', $userId)
            ->sum('amount');
    }

}
