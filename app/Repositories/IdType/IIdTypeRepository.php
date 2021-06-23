<?php

namespace App\Repositories\IdType;

use App\Repositories\IRepository;

interface IIdTypeRepository extends IRepository
{
    public function getIdType($is_primary = 1);
    public function IdTypeForFarmers();
}
