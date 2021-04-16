<?php

namespace App\Repositories\InAddMoney;

use App\Repositories\IRepository;

interface IInAddMoneyRepository extends IRepository
{
    public function getLastByReferenceNumber();
    public function getByReferenceNumber(string $referenceNumber);
}