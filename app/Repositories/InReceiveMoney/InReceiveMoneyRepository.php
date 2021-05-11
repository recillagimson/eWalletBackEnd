<?php

namespace App\Repositories\InReceiveMoney;

use App\Enums\TransactionStatuses;
use App\Repositories\Repository;
use App\Models\InReceiveMoney;

class InReceiveMoneyRepository extends Repository implements IInReceiveMoneyRepository
{
    public function __construct(InReceiveMoney $model)
    {
        parent::__construct($model);
    }

    public function getByUserAccountIDBetweenDates(string $userAccountID, string $startDate, string $endDate)
    {
        return $this->model
            ->where('user_account_id', $userAccountID)
            ->where('status', '!=', TransactionStatuses::failed)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }
}
