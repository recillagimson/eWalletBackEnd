<?php

namespace App\Repositories\UserUtilities\NatureOfWork;

use App\Models\UserUtilities\NatureOfWork;
use App\Repositories\Repository;

class NatureOfWorkRepository extends Repository implements INatureOfWorkRepository
{
    public function __construct(NatureOfWork $model)
    {
        parent::__construct($model);
    }

}
