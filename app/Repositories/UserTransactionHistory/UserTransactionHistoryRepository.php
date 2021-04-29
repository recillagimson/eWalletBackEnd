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

    public function getByAuthUser() {
        $records = $this->model->with(['transaction_category'])->where('user_account_id', request()->user()->id)->get();
        return $records;
    }

    public function findTransactionWithRelation(string $id) {
        $record = $this->model->with(['transaction_category'])->where('id', $id)->first();
        if(!$record) {
            ValidationException::withMessages([
                'record_not_found' => 'Record not found'
            ]);
        }
        return $record;
    }

    public function getTotalTransactionAmountByUserAccountIdDateRange(string $userAccountId, string $from, $to) {
        $total = $this->model->whereBetween('created_at', [$from, $to])->sum('total_amount');
        return $total;
    }

}
