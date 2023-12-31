<?php

namespace App\Repositories\UserBalance;

use App\Repositories\IRepository;

interface IUserBalanceRepository extends IRepository
{
    public function getByUser(string $userId);
}
