<?php

namespace App\Repositories\Tier;

use App\Repositories\IRepository;

interface ITierRepository extends IRepository
{
    public function list($params = []);
}
