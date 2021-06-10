<?php

namespace App\Repositories\OutBuyLoad;

use App\Enums\TransactionStatuses;
use App\Models\OutBuyLoad;
use App\Repositories\Repository;
use Carbon\Carbon;

class OutBuyLoadRepository extends Repository implements IOutBuyLoadRepository
{
    public function __construct(OutBuyLoad $model)
    {
        parent::__construct($model);
    }

    public function getPending(string $userId)
    {
        return $this->model->where([
            'user_account_id' => $userId,
            'status' => TransactionStatuses::pending
        ])->get();
    }

    public function createTransaction(string $userId, string $refNo, string $productCode, string $productName,
                                      string $recipientMobileNumber, float $amount, Carbon $transactionDate,
                                      string $transactionCategoryId, string $userCreated): OutBuyLoad
    {
        $data = [
            'user_account_id' => $userId,
            'reference_number' => $refNo,
            'product_code' => $productCode,
            'product_name' => $productName,
            'recipient_mobile_number' => $recipientMobileNumber,
            'total_amount' => $amount,
            'transaction_date' => $transactionDate,
            'transaction_category_id' => $transactionCategoryId,
            'status' => TransactionStatuses::pending,
            'user_created' => $userCreated
        ];

        return $this->create($data);
    }

    public function getSumOfTransactions(string $from, string $to, string $userAccountId) {
        return $this->model->where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to)
            ->where('status', '!=', 'FAILED')
            ->where('user_account_id', $userAccountId)
            ->sum('total_amount');
    }

}
