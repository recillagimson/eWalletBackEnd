<?php

namespace App\Repositories\UserAccount;

use App\Models\LogHistory;
use App\Repositories\Repository;

class LogHistoryRepository extends Repository implements ILogHistoryRepository
{
    public function __construct(LogHistory $model)
    {
        parent::__construct($model);
    }
}
