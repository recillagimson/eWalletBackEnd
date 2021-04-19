<?php

namespace App\Repositories\ServiceFee;

use App\Models\ServiceFee;
use App\Repositories\IRepository;

interface IServiceFeeRepository extends IRepository
{
    public function getByTierAndTransCategoryID(string $tier_id, string $transCategoryID);
    public function list($params = []);
    public function getAmountByTransactionAndUserAccountId(string $transactionCategoryId, string $userAccountId);
}
