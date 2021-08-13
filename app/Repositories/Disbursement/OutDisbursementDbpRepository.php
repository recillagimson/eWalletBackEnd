<?php

namespace App\Repositories\Disbursement;

use App\Models\OutDisbursementDbp;
use App\Repositories\Repository;


class OutDisbursementDbpRepository extends Repository implements IOutDisbursementDbpRepository
{
    public function __construct(OutDisbursementDbp $model)
    {
        parent::__construct($model);
    }


}
