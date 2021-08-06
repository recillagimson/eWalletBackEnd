<?php

namespace App\Repositories\UserTransactionHistory;

use App\Models\DRCRProcedure;
use App\Models\UserTransactionHistory;
use App\Models\UserUtilities\UserTransactionHistoryView;
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
            'transaction_id' => $transactionId,
            'reference_number' => $refNo,
            'total_amount' => $totalAmount,
            'transaction_category_id' => $transactionCategoryId,
            'transaction_date' => $transactionDate,
            'user_created' => $userCreated
        ];

        return $this->create($data);
    }

    public function getByAuthUser() {
        return $this->model->with(['transaction_category'])
            ->where('user_account_id', request()->user()->id)
            ->orderBy('transaction_date', 'DESC')
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

    private function countTransactionHistoryByDateRangeWithAmountLimitBaseQuery(string $from, string $to, $amount_limit=500000): Builder {
        // return $this->model
        //     ->select(DB::raw('SUM(total_amount) as amount, transaction_date, user_account_id, transaction_category_id'))
        //     ->whereBetween('transaction_date', [$from, $to])
        //     ->groupBy('transaction_date', 'user_account_id')
        //     ->having('amount', '>=', $amount_limit);
            // ->groupBy(function($val) {
            //     return Carbon::parse($val->transaction_date)->format('Y-m-d');
            // })      

        $records = $this->model
        ->with(['user_account', 'user_details'])
        ->whereBetween('created_at', [$from, $to])
        ->where('total_amount', '>=', $amount_limit);

        return $records;
    }

    public function isExisting(string $id)
    {
        if ($this->model->where('transaction_id', $id)->first()) return true;
        return false;
    }

    public function getByAuthUserViaViews(string $status) {
        if($status == 'ALL') {
            return UserTransactionHistoryView::with(['transaction_category'])
                ->where('user_account_id', request()->user()->id)
                ->orderBy('transaction_date', 'DESC')
                ->paginate();
        }
        return UserTransactionHistoryView::with(['transaction_category'])
            ->where('user_account_id', request()->user()->id)
            ->where('status', $status)
            ->orderBy('transaction_date', 'DESC')
            ->paginate();
    }

    public function findTransactionWithRelationViaView(string $id) {
        $record = UserTransactionHistoryView::with(['transaction_category'])->where('transaction_id', $id)->first();

        if(is_null($record)) {
            throw ValidationException::withMessages([
                'record_not_found' => 'Record not found'
            ]);
        }
        return $record->append('transactable');
    }

    public function getTransactionHistoryAdmin(array $attr, bool $paginated = true) {
        $records = DRCRProcedure::with([]);
        $from = Carbon::now()->subDays(30)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');

        if(isset($attr['from']) && isset($attr['to']) && $attr['from'] != '' && $attr['to'] != '') {
            $from = $attr['from'];
            $to = $attr['to'];
        }

        if(isset($attr['filter_by']) && $attr['filter_by'] != '' && isset($attr['filter_value']) && $attr['filter_value'] != '') {
            $filter_by = $attr['filter_by'];
            $filter_value = $attr['filter_value'];

            // IF CUSTOMER NAME
            if($filter_by == 'CUSTOMER_NAME') {
                $records = $records->where(function($q) use($filter_value) {
                    $q->where('first_name', 'LIKE', '%' . $filter_value . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $filter_value . '%');
                });
            }

            // IF CUSTOMER ACCOUNT NUMBER
            else if($filter_by == 'CUSTOMER_ID') {
                $records = $records->where('account_number', $filter_value);
            }

            // IF TYPE
            else if($filter_by == 'TYPE') {
                $records = $records->where('Type', $filter_value);
            }

            // IF STATUS
            else if($filter_by == 'STATUS') {
                $records = $records->where('Status', $filter_value);
            }
        }

        $records = $records->where('reference_number', '!=', 'BEGINNING BALANCE');

        if($paginated) {
            return $records->paginate();
        }

        return $records->get();
    }

}
