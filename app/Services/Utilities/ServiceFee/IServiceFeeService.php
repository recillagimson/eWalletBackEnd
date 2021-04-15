<?php

namespace App\Services\Utilities\ServiceFeeService;

interface IServiceFeeService {
    public function getAmountByTransactionAndTier(string $transactionCategoryId, string $userAccountId);
}
