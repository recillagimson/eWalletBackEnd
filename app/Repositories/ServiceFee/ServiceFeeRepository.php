<?php

namespace App\Repositories\ServiceFee;

use App\Models\ServiceFee;
use App\Repositories\Repository;

class ServiceFeeRepository extends Repository implements IServiceFeeRepository
{
    public function __construct(ServiceFee $model)
    {
        parent::__construct($model);
    }
}
