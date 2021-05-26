<?php

namespace App\Repositories\Tier;

use App\Repositories\IRepository;

interface ITierRequirementRepository extends IRepository
{
    public function getTierRequirements();
}
