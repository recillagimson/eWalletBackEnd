<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard\Dashboard2022View;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class Dashboard2022Repository extends Repository implements IDashboard2022Repository
{
    public function __construct(Dashboard2022View $model)
    {
        parent::__construct($model);
    }
}
