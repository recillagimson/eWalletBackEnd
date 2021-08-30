<?php

namespace App\Repositories\Dashboard;

use App\Repositories\IRepository;

interface IDashboardRepository extends IRepository
{
    public function getDashboardData();
}
