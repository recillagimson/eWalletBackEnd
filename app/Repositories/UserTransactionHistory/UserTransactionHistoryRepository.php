<?php

namespace App\Repositories\UserTransactionHistory;

use App\Models\UserTransactionHistory;
use App\Repositories\Repository;

class UserTransactionHistoryRepository extends Repository implements IUserTransactionHistoryRepository
{
    public function __construct(UserTransactionHistory $model)
    {
        parent::__construct($model);
    }

    public function getTotalTransactionAmountByUserAccountIdDateRange(string $userAccountId, string $from, $to) {
        $total = $this->model->whereBetween('created_at', [$from, $to])->sum('total_amount');
        return $total;
    }
}
