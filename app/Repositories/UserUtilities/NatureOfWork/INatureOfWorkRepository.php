<?php

namespace App\Repositories\UserUtilities\NatureOfWork;

use App\Repositories\IRepository;

interface INatureOfWorkRepository extends IRepository
{
    public function getAllNaturesofWork();  
}
