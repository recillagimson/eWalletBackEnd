<?php

namespace App\Repositories\Disbursement;

use App\Models\InDisbursementDbps;
use App\Repositories\Repository;


class InDisbursementDbpRepository extends Repository implements IInDisbursementDbpRepository
{
    public function __construct(InDisbursementDbps $model)
    {
        parent::__construct($model);
    }


}
