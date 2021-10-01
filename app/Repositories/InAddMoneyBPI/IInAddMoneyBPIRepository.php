<?php

namespace App\Repositories\InAddMoneyBPI;

use App\Repositories\IRepository;

interface IInAddMoneyBPIRepository extends IRepository
{
    public function getSumOfTransactions(string $from, string $to, string $userId);
}
