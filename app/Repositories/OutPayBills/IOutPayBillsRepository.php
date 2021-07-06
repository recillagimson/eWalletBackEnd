<?php

namespace App\Repositories\OutPayBills;

use App\Repositories\IRepository;

interface IOutPayBillsRepository extends IRepository
{
    public function getSumOfTransactions($from, $to, string $userAccountId);
    public function getPending(string $userId);
    public function getAllBillersWithPaginate();
    public function getAllBillers();
}
