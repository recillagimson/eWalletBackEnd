<?php

namespace App\Repositories\Tier;

use App\Models\Tier;
use App\Models\UserAccount;
use App\Repositories\Repository;

class TierRepository extends Repository implements ITierRepository
{
    public function __construct(Tier $model)
    {
        parent::__construct($model);
    }

    public function list($params = []) {
        if(isset($params['search']) && $params['search'] != "") {
            return $this->model->where('name', 'LIKE', '%' . $params['search'] . '%')->get();
        } else {
            return $this->model->get();
        }
    }
}
