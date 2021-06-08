<?php

namespace App\Services\Utilities\ServiceFee;

interface IServiceFeeService
{
    public function getAmountByTransactionAndUserAccountId(string $transactionCategoryId, string $tierId);
}
