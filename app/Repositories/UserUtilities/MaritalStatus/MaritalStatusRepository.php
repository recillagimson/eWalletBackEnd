<?php

namespace App\Repositories\UserUtilities\MaritalStatus;

use App\Models\UserUtilities\MaritalStatus;
use App\Repositories\Repository;

class MaritalStatusRepository extends Repository implements IMaritalStatusRepository
{
    public function __construct(MaritalStatus $model)
    {
        parent::__construct($model);
    }

    public function getAllMaritalStatuses()
    {
        return $this->model->orderBy('description')->get();
    }

}
