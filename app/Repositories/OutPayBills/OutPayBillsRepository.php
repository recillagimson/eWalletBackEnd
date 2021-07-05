<?php

namespace App\Repositories\OutPayBills;

use App\Enums\TransactionStatuses;
use App\Models\OutPayBills;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Builder;

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

    public function getPending(string $userId)
    {
        return $this->model->where([ 'user_account_id' => $userId, 'status' => TransactionStatuses::pending ])->get();
    }

    public function getAllBillersWithPaginate() {
        return $this->getAllBillersBaseQuery()->paginate();
    }

    public function getAllBillers() {
        return $this->getAllBillersBaseQuery()->get();
    }

    private function getAllBillersBaseQuery(): Builder {
        return $this->model->with(['user_detail', 'user_account']);
    }

}
