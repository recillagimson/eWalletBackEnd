<?php

namespace App\Repositories\UserUtilities\Nationality;

use App\Models\UserUtilities\Nationality;
use App\Repositories\Repository;

class NationalityRepository extends Repository implements INationalityRepository
{
    public function __construct(Nationality $model)
    {
        parent::__construct($model);
    }

    public function getAllNationalities()
    {
        return $this->model->orderBy('description')->all();
    }

}
