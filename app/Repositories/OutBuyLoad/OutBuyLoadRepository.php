<?php

namespace App\Repositories\OutBuyLoad;

use App\Enums\TransactionStatuses;
use App\Models\OutBuyLoad;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;

class OutBuyLoadRepository extends Repository implements IOutBuyLoadRepository
{
    public function __construct(OutBuyLoad $model)
    {
        parent::__construct($model);
    }

    public function getByUserAccountIDBetweenDates(string $userId, Carbon $startDate, Carbon $endDate)
    {
        return $this->model
            ->where('user_account_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }
}
