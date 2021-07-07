<?php

namespace App\Repositories\LogHistory;

use App\Models\LogHistory;
use App\Repositories\Repository;

class LogHistoryRepository extends Repository implements ILogHistoryRepository
{
    public function __construct(LogHistory $model)
    {
        parent::__construct($model);
    }

    public function getByUserAccountId(string $userAccountId) {
        $records = $this->model;
        if($userAccountId != "0") {
            $records = $records->where('user_account_id', $userAccountId);
        }
        $records = $records->get();
        return $records;
    }
}
