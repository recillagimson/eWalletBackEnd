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

    public function list($params = []) {
        if(isset($params['search']) && $params['search'] != "") {
            return $this->model->whereHas('tier', function($query) use($params) {
                $query->where('name', 'LIKE', '%' . $params['search'] . '%');
            })->get();
        } else {
            return $this->model->get();
        }
    }
}
