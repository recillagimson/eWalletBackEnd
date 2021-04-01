<?php

namespace App\Repositories\HelpCenter;

use App\Models\HelpCenter;
use App\Repositories\Repository;

class HelpCenterRepository extends Repository implements IHelpCenterRepository
{
    public function __construct(HelpCenter $model)
    {
        parent::__construct($model);
    }
}
