<?php

namespace App\Repositories\InReceiveFromDBP;

use App\Repositories\Repository;
use App\Models\InReceiveFromDBP;
use Carbon\Carbon;

class InReceiveFromDBPRepository extends Repository implements IInReceiveFromDBPRepository
{
    public function __construct(InReceiveFromDBP $model)
    {
        parent::__construct($model);
    }
}