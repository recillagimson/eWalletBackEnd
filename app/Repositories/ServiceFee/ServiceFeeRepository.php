<?php

namespace App\Repositories\ServiceFee;

use App\Repositories\Repository;
use App\Models\ServiceFee;

class ServiceFeeRepository extends Repository implements IServiceFeeRepository
{
    public function __construct(ServiceFee $model)
    {
        parent::__construct($model);
    }

    public function getByTierAndTransCategoryID(int $tier, string $tranCategoryID)
    {
        return $this->model->where('tier', $tier)->where('transaction_category_id', $tranCategoryID)->first();
    }
}
