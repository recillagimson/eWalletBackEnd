<?php

namespace App\Repositories\Dashboard;

use App\Models\Client;
use App\Models\DashboardView;
use App\Repositories\Repository;

class DashboardRepository extends Repository implements IDashboardRepository
{
    public function __construct(DashboardView $model)
    {
        parent::__construct($model);
    }

    public function getDashboardData() {
        return $this->model->first();
    }
}
