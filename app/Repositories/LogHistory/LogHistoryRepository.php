<?php

namespace App\Repositories\LogHistory;

use App\Repositories\Repository;
use App\Models\LogHistory;

class LogHistoryRepository extends Repository implements ILogHistoryRepository
{
    public function __construct(LogHistory $model)
    {
        parent::__construct($model);
    }
}
