<?php

namespace App\Repositories\OutBuyLoad;

use App\Models\OutBuyLoad;
use App\Repositories\Repository;

class OutBuyLoadRepository extends Repository implements IOutBuyLoadRepository
{
    public function __construct(OutBuyLoad $model)
    {
        parent::__construct($model);
    }
}
