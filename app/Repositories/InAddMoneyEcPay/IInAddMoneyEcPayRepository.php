<?php

namespace App\Repositories\InAddMoneyEcPay;

use App\Repositories\IRepository;

interface IInAddMoneyEcPayRepository extends IRepository
{
    public function getDataByReferenceNumber(string $referenceNumber);
    public function getSumOfTransactions(string $from, string $to, string $userId);
}
