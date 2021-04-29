<?php

namespace App\Repositories\UserTransactionHistory;

use App\Repositories\IRepository;

interface IUserTransactionHistoryRepository extends IRepository
{
    public function getTotalTransactionAmountByUserAccountIdDateRange(string $userAccountId, string $from, $to);

    public function log(string $userId, string $transactionCategoryId, string $transactionId, string $refNo,
                        float $totalAmount, string $userCreated);
}
