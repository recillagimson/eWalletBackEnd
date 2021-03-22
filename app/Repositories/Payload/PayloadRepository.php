<?php

namespace App\Repositories\Payload;

use App\Models\Payload;
use App\Repositories\Repository;

class PayloadRepository extends Repository implements IPayloadRepository
{
    public function __construct(Payload $model)
    {
        parent::__construct($model);
    }
}
