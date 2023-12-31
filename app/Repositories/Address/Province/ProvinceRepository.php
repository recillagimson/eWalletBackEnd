<?php

namespace App\Repositories\Address\Province;

use App\Models\Province;
use App\Repositories\Repository;

class ProvinceRepository extends Repository implements IProvinceRepository
{
    public function __construct(Province $model)
    {
        parent::__construct($model);
    }

    public function getProvinces()
    {
        return $this->model->get();
    }

    public function getProvinceByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }
}
