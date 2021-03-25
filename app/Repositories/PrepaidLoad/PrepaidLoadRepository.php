<?php

namespace App\Repositories\PrepaidLoad;

use App\Models\PrepaidLoad;
use App\Repositories\Repository;

class PrepaidLoadRepository extends Repository implements IPrepaidLoadRepository
{
    public function __construct(PrepaidLoad $model)
    {
        parent::__construct($model);
    }
}
