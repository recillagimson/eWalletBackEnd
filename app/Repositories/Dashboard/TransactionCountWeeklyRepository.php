<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard\TransactionCountWeeklyView;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class TransactionCountWeeklyRepository extends Repository implements ITransactionCountWeeklyRepository
{
    public function __construct(TransactionCountWeeklyView $model)
    {
        parent::__construct($model);
    }
}
