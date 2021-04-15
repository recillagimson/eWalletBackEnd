<?php

namespace App\Services\Utilities\ServiceFeeService;

use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;

class ServiceFeeService implements IServiceFeeService
{
    public IServiceFeeRepository $serviceFeeRepository;

    public function __construct(ILogHistoryRepository $serviceFeeRepository)
    {
        $this->serviceFeeRepository = $serviceFeeRepository;
    }

    public function getAmountByTransactionAndUserAccountId(string $transactionCategoryId, string $userAccountId) {
        return $this->serviceFeeRepository->getAmountByTransactionAndUserAccountId($transactionCategoryId, $userAccountId);
    }
}
