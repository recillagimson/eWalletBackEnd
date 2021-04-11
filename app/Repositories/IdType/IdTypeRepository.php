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
}
