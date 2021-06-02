<?php

namespace App\Repositories\OutPayBills;

use App\Repositories\IRepository;

interface IOutPayBillsRepository extends IRepository
{
    public function getSumOfTransactions($from, $to);
    public function getPending(string $userId);
}
