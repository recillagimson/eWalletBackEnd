<?php

namespace App\Repositories\DBP;

use App\Repositories\IRepository;

interface IDBPRepository extends IRepository
{
    public function customerList($from, $to, $filterBy, $filterValue, $isPaginated = false);
}
