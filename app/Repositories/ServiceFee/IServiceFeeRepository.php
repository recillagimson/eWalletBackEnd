<?php

namespace App\Repositories\ServiceFee;

use App\Models\ServiceFee;
use App\Repositories\IRepository;

interface IServiceFeeRepository extends IRepository
{
    public function getByTierAndTransCategoryID(string $tierID, string $transCategoryID);
    public function list($params = []);
    public function getAmountByTransactionAndUserAccountId(string $transactionCategoryId, string $tierId);
}
