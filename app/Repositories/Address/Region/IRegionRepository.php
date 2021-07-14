<?php

namespace App\Repositories\Address\Region;

use App\Repositories\IRepository;

interface IRegionRepository extends IRepository
{
    public function getRegions();
}
