<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard\TransactionCountMonthlyView;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class TransactionCountMonthlyRepository extends Repository implements ITransactionCountMonthlyRepository
{
    public function __construct(TransactionCountMonthlyView $model)
    {
        parent::__construct($model);
    }
}
