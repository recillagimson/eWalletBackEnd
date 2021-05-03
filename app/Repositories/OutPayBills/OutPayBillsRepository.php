<?php

namespace App\Repositories\OutPayBills;

use App\Models\OutPayBills;
use App\Repositories\Repository;

class OutPayBillsRepository extends Repository implements IOutPayBillsRepository
{
    public function __construct(OutPayBills $model)
    {
        parent::__construct($model);
    }

}
