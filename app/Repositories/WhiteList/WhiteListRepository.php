<?php

namespace App\Repositories\WhiteList;

use App\Models\Client;
use App\Models\WhiteList;
use App\Repositories\Repository;

class WhiteListRepository extends Repository implements IWhiteListRepository
{
    public function __construct(WhiteList $model)
    {
        parent::__construct($model);
    }
}
