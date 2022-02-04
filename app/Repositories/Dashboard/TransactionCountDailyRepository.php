<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard\TransactionCountDailyView;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class TransactionCountDailyRepository extends Repository implements ITransactionCountDailyRepository
{
    public function __construct(TransactionCountDailyView $model)
    {
        parent::__construct($model);
    }

}
