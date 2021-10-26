<?php

namespace App\Repositories\OutPayBills;

use App\Repositories\IRepository;

interface IOutPayBillsRepository extends IRepository
{
    public function getSumOfTransactions($from, $to, string $userAccountId);
    public function getPending(string $userId);
    public function getAllBillersWithPaginate();
    public function getAllBillers();
    public function totalPayBills();
    public function totalamountPayBills();
    public function totalotherchargesPayBills();
    public function totalservicefeePayBills();
    public function reportData(string $from, string $to, string $filterBy = '', string $filterValue = '');
}
