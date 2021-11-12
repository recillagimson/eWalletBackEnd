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

    public function getSumOfTransactions(string $from, string $to, string $userId) {
        return $this->model->where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to)
            ->where('status', '!=', 'error')
            ->where('user_account_id', $userId)
            ->sum('amount');
    }

    public function getRefNoInPendingStatusFromUser(string $userId) {
        return $this->model
            ->select('reference_number')
            ->where('user_account_id', '=', $userId)
            ->where('status', '=', 'pending')
            ->get();
    }
}
