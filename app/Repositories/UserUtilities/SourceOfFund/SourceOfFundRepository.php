<?php

namespace App\Repositories\UserUtilities\SourceOfFund;

use App\Models\UserUtilities\SourceOfFund;
use App\Repositories\Repository;

class SourceOfFundRepository extends Repository implements ISourceOfFundRepository
{
    public function __construct(SourceOfFund $model)
    {
        parent::__construct($model);
    }

}
