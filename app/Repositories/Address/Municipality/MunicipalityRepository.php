<?php

namespace App\Repositories\Address\Municipality;

use App\Models\Municipality;
use App\Repositories\Repository;

class MunicipalityRepository extends Repository implements IMunicipalityRepository
{
    public function __construct(Municipality $model)
    {
        parent::__construct($model);
    }

    public function getMunicipalities(string $code)
    {
        return $this->model->where('province_code', '=', $code)->get();
    }
}
