<?php

namespace App\Repositories\Address\Region;

use App\Models\Region;
use App\Repositories\Repository;

class RegionRepository extends Repository implements IRegionRepository
{
    public function __construct(Region $model)
    {
        parent::__construct($model);
    }

    public function getRegions()
    {
        return $this->model->all();
    }
}
