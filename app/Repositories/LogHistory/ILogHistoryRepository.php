<?php

namespace App\Repositories\LogHistory;

use App\Repositories\IRepository;

interface ILogHistoryRepository extends IRepository
{
    public function getByUserAccountId(string $userAccountId);
}
