<?php

namespace App\Repositories\SecurityBank;

use App\Repositories\IRepository;

interface IPesoNetBankRepository extends IRepository
{
    public function getListSorted($sortBy = 'bank_name', $sortDirection = 'ASC');
}
