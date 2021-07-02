<?php

namespace App\Repositories\UserTransactionHistory;

use App\Models\UserTransactionHistory;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use DB;
use Illuminate\Database\Eloquent\Builder;

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
                        float $totalAmount, Carbon $transactionDate, string $userCreated)
    {
        $data = [
            'user_account_id' => $userId,
            'transaction_category_id' => $transactionCategoryId,
            'transaction_id' => $transactionId,
            'reference_number' => $refNo,
            'total_amount' => $totalAmount,
            'transaction_date' => $transactionDate,
            'user_created' => $userCreated
        ];

        return $this->create($data);
    }

    public function getByAuthUser() {
        return $this->model->with(['transaction_category'])
            ->where('user_account_id', request()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate();
    }

    public function findTransactionWithRelation(string $id) {
        $record = $this->model->with(['transaction_category'])->find($id);

        if(is_null($record)) {
            throw ValidationException::withMessages([
                'record_not_found' => 'Record not found'
            ]);
        }
        return $record->append('transactable');
    }

    public function getTransactionHistoryByIdAndDateRange(string $userAccountId, string $from, string $to) {
        return $this->model
            ->with(['transaction_category'])
            ->where('user_account_id', $userAccountId)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->get();
    }

    public function countTransactionHistoryByDateRangeWithAmountLimitWithPaginate(string $from, string $to) {
        return $this->countTransactionHistoryByDateRangeWithAmountLimitBaseQuery($from, $to)->paginate();
    }

    public function countTransactionHistoryByDateRangeWithAmountLimit(string $from, string $to) {
        return $this->countTransactionHistoryByDateRangeWithAmountLimitBaseQuery($from, $to)->get();
    }

    private function countTransactionHistoryByDateRangeWithAmountLimitBaseQuery(string $from, string $to, $amount_limit=50000): Builder {
        // return $this->model
        //     ->select(DB::raw('SUM(total_amount) as amount, transaction_date, user_account_id, transaction_category_id'))
        //     ->whereBetween('transaction_date', [$from, $to])
        //     ->groupBy('transaction_date', 'user_account_id')
        //     ->having('amount', '>=', $amount_limit);
            // ->groupBy(function($val) {
            //     return Carbon::parse($val->transaction_date)->format('Y-m-d');
            // })      

        $records = $this->model
        ->with(['user_account'])
        ->whereBetween('created_at', [$from, $to])
        ->where('total_amount', '>=', $amount_limit);

        return $records;
    }

    public function isExisting(string $id)
    {
       if($this->model->where('transaction_id', $id)->first()) return true;
       return false;
    }

}
