<?php

namespace App\Repositories\IdType;

use App\Models\IdType;
use App\Repositories\Repository;
use App\Repositories\IdType\IIdTypeRepository;

class IdTypeRepository extends Repository implements IIdTypeRepository
{
    public function __construct(IdType $model)
    {
        parent::__construct($model);
    }

    public function getIdType($is_primary = 1) {
        return $this->model->where('is_primary', $is_primary)->get();
    }
}
