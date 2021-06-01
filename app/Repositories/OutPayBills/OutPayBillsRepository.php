<?php

namespace App\Repositories\OutPayBills;

use App\Models\OutPayBills;
use App\Repositories\Repository;

class OutPayBillsRepository extends Repository implements IOutPayBillsRepository
{
    public function __construct(OutPayBills $model)
    {
        parent::__construct($model);
    }

    public function getSumOfTransactions($from, $to, string $userAccountId) {
        return $this->model->where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to)
            ->where('status', '!=', 'FAILED')
            ->where('user_account_id', $userAccountId)
            ->sum('total_amount');
    }
}
