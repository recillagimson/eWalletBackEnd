<?php

namespace App\Repositories\InAddMoneyBPI;

use App\Enums\TransactionStatuses;
use App\Models\InAddMoneyBPI;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

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

    public function getPromoTransaction(Carbon $from, Carbon $to, float $minAmount, string $currentTransactionId): Collection
    {
        return $this->model
            ->whereDate('transaction_date', '>=', $from->toDateString())
            ->whereDate('transaction_date', '<=', $to->toDateString())
            ->where('status', TransactionStatuses::success)
            ->where('amount', '>=', $minAmount)
            ->where('id', '!=', $currentTransactionId)
            ->get();
    }
}
