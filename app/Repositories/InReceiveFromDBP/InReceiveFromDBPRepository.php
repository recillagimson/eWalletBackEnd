<?php

namespace App\Repositories\InReceiveFromDBP;

use App\Repositories\Repository;
use App\Models\InReceiveFromDBP;
use Carbon\Carbon;

class InReceiveFromDBPRepository extends Repository implements IInReceiveFromDBPRepository
{
    public function __construct(InReceiveFromDBP $model)
    {
        parent::__construct($model);
    }

    public function getExistByTransactionCategory($userId, $transactionCategoryId) 
    {
        return $this->model
                    ->where('user_account_id', $userId)
                    ->where('transaction_category_id', $transactionCategoryId)
                    ->count();
    }
}