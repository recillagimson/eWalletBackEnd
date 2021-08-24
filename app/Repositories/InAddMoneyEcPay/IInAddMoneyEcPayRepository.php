<?php

namespace App\Repositories\InAddMoneyEcPay;

use App\Repositories\IRepository;

interface IInAddMoneyEcPayRepository extends IRepository
{
    public function getDataByReferenceNumber(string $referenceNumber);
}
