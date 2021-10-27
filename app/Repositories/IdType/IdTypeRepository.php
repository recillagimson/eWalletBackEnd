<?php

namespace App\Repositories\IdType;

use App\Enums\IdTypes;
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
        if($is_primary == 2) {
            return $this->model->get();
        }
        return $this->model->where('is_primary', $is_primary)->get();
    }

    public function IdTypeForFarmers() {
        return $this->model
            // ->whereIn('id', [
            //     IdTypes::tin,
            //     IdTypes::sss,
            //     IdTypes::umid,
            //     IdTypes::drivers,
            // ])
            ->get();
    }
}
