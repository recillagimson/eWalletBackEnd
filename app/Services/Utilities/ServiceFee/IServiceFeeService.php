<?php

namespace App\Services\Utilities\ServiceFeeService;

interface IServiceFeeService {
    public function getAmountByTransactionAndUserAccountId(string $transactionCategoryId, string $userAccountId);
}
