<?php

namespace App\Repositories\OutBuyLoad;

use App\Enums\TransactionStatuses;
use App\Models\OutBuyLoad;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

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

    public function getUsersWithPending()
    {
        return $this->model
            ->where('status', TransactionStatuses::pending)
            ->groupBy('user_account_id')
            ->select('user_account_id')
            ->get();

    }

    public function createTransaction(string $userId, string $refNo, string $productCode, string $productName,
                                      string $recipientMobileNumber, float $amount, Carbon $transactionDate,
                                      string $transactionCategoryId, string $type, string $userCreated): OutBuyLoad
    {
        $data = [
            'user_account_id' => $userId,
            'reference_number' => $refNo,
            'product_code' => $productCode,
            'product_name' => $productName,
            'topup_type' => $type,
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

    public function totalBuyload()
    {
        return $this->model->where('transaction_date','<=',Carbon::now()->subDay())->where('status','=','SUCCESS')->sum('total_amount');
    }

    public function getSmartPromoTransaction(string $userId, int $month, int $year, string $currentTransactionId): Collection
    {
        return $this->model
            ->where('user_account_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->where('status', TransactionStatuses::success)
            ->where('id', '!=', $currentTransactionId)
            ->get();
    }
}
