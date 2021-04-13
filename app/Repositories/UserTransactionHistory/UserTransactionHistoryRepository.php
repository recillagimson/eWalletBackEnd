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
}
