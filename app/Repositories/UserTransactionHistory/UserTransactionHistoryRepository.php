<?php

namespace App\Repositories\UserTransactionHistory;

use App\Models\UserTransactionHistory;
use App\Repositories\Repository;
use Illuminate\Validation\ValidationException;

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

    public function getByAuthUser() {
        return $this->model->with(['transaction_category'])->where('user_account_id', request()->user()->id)->orderBy('created_at', 'DESC')->paginate();
    }

    public function findTransactionWithRelation(string $id) {
        $record = $this->model->with(['transaction_category'])->where('id', $id)->first();
        if(!$record) {
            ValidationException::withMessages([
                'record_not_found' => 'Record not found'
            ]);
        }
        return $record->append('transactable');
    }

}
