<?php

namespace App\Repositories\NewsAndUpdate;

use App\Models\NewsAndUpdate;
use App\Repositories\Repository;

class NewsAndUpdateRepository extends Repository implements INewsAndUpdateRepository
{
    public function __construct(NewsAndUpdate $model)
    {
        parent::__construct($model);
    }
}
