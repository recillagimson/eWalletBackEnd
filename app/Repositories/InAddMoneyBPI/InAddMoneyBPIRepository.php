<?php

namespace App\Repositories\InAddMoneyBPI;

use App\Models\InAddMoneyBPI;
use App\Repositories\Repository;

class InAddMoneyBPIRepository extends Repository implements IInAddMoneyBPIRepository
{
    public function __construct(InAddMoneyBPI $model)
    {
        parent::__construct($model);
    }
}
