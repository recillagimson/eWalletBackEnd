<?php

namespace App\Repositories\UserTransactionHistory;

use Carbon\Carbon;
use App\Enums\DBPUploadKeys;
use App\Models\DRCRProcedure;
use App\Repositories\Repository;
use App\Models\UserTransactionHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use App\Models\UserUtilities\UserTransactionHistoryView;

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


    // $this->transactionHistory->log(
        // request()->user()->id, 
        // TransactionCategoryIds::cashinBPI, 
        // $params['transactionId'], 
        // $params['refId'], 
        // $params['amount'], 
        // Carbon::now(), 
        // request()->user()->id);

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
        // dd(request()->user()->id);
        if($status == 'ALL') {
            return UserTransactionHistoryView::with(['transaction_category'])
                ->where('user_account_id', request()->user()->id)
                ->orderBy('original_transaction_date', 'desc')
                ->paginate();
        }
        return UserTransactionHistoryView::with(['transaction_category'])
            ->where('user_account_id', request()->user()->id)
            ->where('status', $status)
            ->orderBy('original_transaction_date', 'desc')
            ->paginate();
    }

    public function findTransactionWithRelationViaView(string $id) {
        $record = UserTransactionHistoryView::with(['transaction_category'])->where('transaction_id', $id)->first();

        if (is_null($record)) {
            throw ValidationException::withMessages([
                'record_not_found' => 'Record not found'
            ]);
        }
        return $record->append('transactable');
    }

    public function getTransactionHistoryAdmin(array $attr, $isPaginated = false)
    {
        $from = Carbon::now()->subDays(30)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');

        if (isset($attr['from']) && isset($attr['to']) && $attr['from'] != '' && $attr['to'] != '') {
            $from = $attr['from'];
            $to = $attr['to'];
        }

        $records = DRCRProcedure::with([])
            ->where('original_transaction_date', '>=', $from)
            ->where('original_transaction_date', '<=', $to);

        if (isset($attr['filter_by']) && $attr['filter_by'] != '' && isset($attr['filter_value']) && $attr['filter_value'] != '') {
            $filter_by = $attr['filter_by'];
            $filter_value = $attr['filter_value'];

            // IF CUSTOMER NAME
            if ($filter_by == 'CUSTOMER_NAME') {
                $records = $records->where(function ($q) use ($filter_value) {
                    $q->where('first_name', 'LIKE', '%' . $filter_value . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $filter_value . '%');
                });
            } // IF CUSTOMER ACCOUNT NUMBER
            else if ($filter_by == 'CUSTOMER_ID') {
                $records = $records->where('account_number', $filter_value);
            }

            // IF TYPE
            else if ($filter_by == 'TYPE') {
                $records = $records->where('Type', $filter_value);
            } // IF STATUS
            else if ($filter_by == 'STATUS') {
                $records = $records->where('Status', $filter_value);
            }
        }

        $records = $records->where('reference_number', '!=', 'BEGINNING BALANCE');

        if($isPaginated) {
            return $records->paginate();
        }
        return $records->get();
    }

    public function getTransactionHistoryAdminFarmer(array $attr)
    {
        $records = DRCRProcedure::with([]);
        $from = Carbon::now()->subDays(30)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');

        if (isset($attr['from']) && isset($attr['to']) && $attr['from'] != '' && $attr['to'] != '') {
            $from = $attr['from'];
            $to = $attr['to'];
        }

        $records = $records->where('original_transaction_date', '>=', $from)
        ->where('original_transaction_date', '<=', $to);

        if (isset($attr['filter_by']) && $attr['filter_by'] != '' && isset($attr['filter_value']) && $attr['filter_value'] != '') {
            $filter_by = $attr['filter_by'];
            $filter_value = $attr['filter_value'];

            // IF CUSTOMER NAME
            if ($filter_by == 'CUSTOMER_NAME') {
                $records = $records->where(function ($q) use ($filter_value) {
                    $q->where('first_name', 'LIKE', '%' . $filter_value . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $filter_value . '%');
                });
            } // IF CUSTOMER ACCOUNT NUMBER
            else if ($filter_by == 'CUSTOMER_ID') {
                $records = $records->where('account_number', $filter_value);
            }

            // IF TYPE
            else if ($filter_by == 'TYPE') {
                $records = $records->where('Type', $filter_value);
            } // IF STATUS
            else if ($filter_by == 'STATUS') {
                $records = $records->where('Status', $filter_value);
            }

            // IF RSBSA_NUMBER
            else if($filter_by == 'RSBSA_NUMBER') {
                $records = $records->where('rsbsa_number', $filter_value);
            }

            // IF TRANSACTION_DESCRIPTION
            else if($filter_by == 'TRANSACTION_DESCRIPTION') {
                $records = $records->where('Description', 'LIKE', '%'. $filter_value .'%');
            }
        }

        $records = $records->where('reference_number', '!=', 'BEGINNING BALANCE')
        ->where('rsbsa_number', '!=', '');

        if($attr && $attr['type'] == 'API') {
            return $records->paginate();
        }
        return $records->get();
    }

    public function getDBPTransactionHistory(array $attr, string $authUser)
    {
        $records = UserTransactionHistoryView::with(['user_detail']);
        $from = Carbon::now()->subDays(30)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');

        if (isset($attr['from']) && isset($attr['to']) && $attr['from'] != '' && $attr['to'] != '') {
            $from = $attr['from'];
            $to = $attr['to'];
        }

        $records = $records->where('transaction_category_id', DBPUploadKeys::DBPTransactionId);

       // $records = $records->where('original_transaction_date', '>=', $from)
       // ->where('original_transaction_date', '<=', $to)
        //->where('transaction_category_id', DBPUploadKeys::DBPTransactionId);

        if (isset($attr['filter_by']) && $attr['filter_by'] != '' && isset($attr['filter_value']) && $attr['filter_value'] != '') {
            $filter_by = $attr['filter_by'];
            $filter_value = $attr['filter_value'];

            // IF CUSTOMER NAME
            // if ($filter_by == 'CUSTOMER_NAME') {
            //     $records = $records->where(function ($q) use ($filter_value) {
            //         $q->where('first_name', 'LIKE', '%' . $filter_value . '%')
            //             ->orWhere('last_name', 'LIKE', '%' . $filter_value . '%');
            //     });
            // } 
            // IF CUSTOMER ACCOUNT NUMBER
            if ($filter_by == 'CUSTOMER_ID') {
                $records = $records->where('account_number', $filter_value);
            }

            // IF TYPE
            // else if ($filter_by == 'TYPE') {
            //     $records = $records->where('Type', $filter_value);
            // } 
            // IF STATUS
            else if ($filter_by == 'STATUS') {
                $records = $records->where('status', $filter_value);
            }

            // IF RSBSA_NUMBER
            else if($filter_by == 'RSBSA_NUMBER') {
                $records = $records->where('rsbsa_number', $filter_value);
            }

            // IF TRANSACTION_DESCRIPTION
            else if($filter_by == 'TRANSACTION_DESCRIPTION') {
                $records = $records->where('Description', 'LIKE', '%'. $filter_value .'%');
            }
        }

        // $records = $records->where('reference_number', '!=', 'BEGINNING BALANCE')
        // ->where('rsbsa_number', '!=', '');

        if($authUser && $authUser != "") {
            $records = $records->where('user_updated', $authUser);
        }

        if($attr && $attr['type'] == 'API') {
            return $records->paginate();
        }
        return $records->get();
    }

}
