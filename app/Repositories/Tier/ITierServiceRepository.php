<?php

namespace App\Repositories\Tier;

use App\Repositories\IRepository;

interface ITierServiceRepository extends IRepository
{
    public function getTierDetails();
}
