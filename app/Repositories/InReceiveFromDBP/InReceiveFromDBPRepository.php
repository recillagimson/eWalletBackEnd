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

    public function getExistByTransactionCategory($userAccountNumber, $transactionCategoryId) 
    {
        return $this->model
                    ->where('transaction_category_id', $transactionCategoryId)
                    ->where('user_account_id', $userAccountNumber)
                    ->count();
    }
}