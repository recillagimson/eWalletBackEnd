<?php

namespace App\Repositories\AddMoney;

use App\Repositories\IRepository;

interface IWebBankRepository extends IRepository
{
    public function getLastByReferenceNumber();
    public function getByReferenceNumber(string $referenceNumber);
}