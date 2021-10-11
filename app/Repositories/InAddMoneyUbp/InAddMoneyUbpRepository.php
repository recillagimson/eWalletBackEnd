<?php

namespace App\Repositories\InAddMoneyUbp;

use App\Enums\TransactionStatuses;
use App\Models\InAddMoneyUbp;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Collection;

class InAddMoneyUbpRepository extends Repository implements IInAddMoneyUbpRepository
{
    public function __construct(InAddMoneyUbp $model)
    {
        parent::__construct($model);
    }

    public function getPending(string $userId): Collection
    {
        return $this->model->where([
            'user_account_id' => $userId,
            'status' => TransactionStatuses::pending
        ])->get();
    }
}
