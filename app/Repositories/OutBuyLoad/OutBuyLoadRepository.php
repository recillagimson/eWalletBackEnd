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

    public function getByUserAccountIDBetweenDates(string $userId, Carbon $startDate, Carbon $endDate)
    {
        return $this->model
            ->where('user_account_id', $userId)
            ->where('status', '!=', TransactionStatuses::failed)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
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

}
