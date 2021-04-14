<?php

namespace App\Repositories\ServiceFee;

use App\Models\ServiceFee;
use App\Repositories\IRepository;

interface IServiceFeeRepository extends IRepository
{
    public function getByTierAndTransCategoryID(int $tier, string $transCategoryID);
    public function list($params = []);
}
