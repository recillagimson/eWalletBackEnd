<?php

namespace App\Services\Utilities\ServiceFee;

use App\Repositories\ServiceFee\IServiceFeeRepository;

class ServiceFeeService implements IServiceFeeService
{
    public IServiceFeeRepository $serviceFeeRepository;

    public function __construct(IServiceFeeRepository $serviceFeeRepository)
    {
        $this->serviceFeeRepository = $serviceFeeRepository;
    }

    public function getAmountByTransactionAndUserAccountId(string $transactionCategoryId, string $tierId) {
        return $this->serviceFeeRepository->getAmountByTransactionAndUserAccountId($transactionCategoryId, $tierId);
    }
}
