<?php

namespace App\Repositories\ServiceFee;

use App\Repositories\IRepository;

interface IServiceFeeRepository extends IRepository
{
    public function list($params = []);
    public function getAmountByTransactionAndTier(string $transactionCategoryId, string $userAccountId);
}
