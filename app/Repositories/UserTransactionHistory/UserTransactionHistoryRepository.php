<?php

namespace App\Repositories\UserTransactionHistory;

use App\Models\UserTransactionHistory;
use App\Repositories\Repository;

class UserTransactionHistoryRepository extends Repository implements IUserTransactionHistoryRepository
{
    public function __construct(UserTransactionHistory $model)
    {
        parent::__construct($model);
    }

    public function getTotalTransactionAmountByUserAccountIdDateRange(string $userAccountId, string $from, $to)
    {
        return $this->model->whereBetween('created_at', [$from, $to])->sum('total_amount');
    }

    public function log(string $userId, string $transactionCategoryId, string $transactionId, string $refNo,
                        float $totalAmount, string $userCreated)
    {
        $data = [
            'user_account_id' => $userId,
            'transaction_id' => $transactionId,
            'reference_number' => $refNo,
            'total_amount' => $totalAmount,
            'transaction_category_id' => $transactionCategoryId,
            'user_created' => $userCreated
        ];

        return $this->create($data);
    }

}
