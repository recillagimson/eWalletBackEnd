<?php

namespace App\Repositories\OutPayBills;

use App\Enums\TransactionStatuses;
use App\Models\BillerReport;
use App\Models\OutPayBills;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OutPayBillsRepository extends Repository implements IOutPayBillsRepository
{
    public function __construct(OutPayBills $model)
    {
        parent::__construct($model);
    }

    public function getSumOfTransactions($from, $to, string $userAccountId) {
        return $this->model->where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to)
            ->where('status', '!=', 'FAILED')
            ->where('user_account_id', $userAccountId)
            ->sum('total_amount');
    }

    public function getPending(string $userId)
    {
        return $this->model->where(['user_account_id' => $userId, 'status' => TransactionStatuses::pending])->get();
    }

    public function getUsersWithPending()
    {
        return $this->model->where('status', TransactionStatuses::pending)
            ->groupBy('user_account_id')
            ->select('user_account_id')
            ->get();

    }

    public function getAllBillersWithPaginate()
    {
        return $this->getAllBillersBaseQuery()->paginate();
    }

    public function getAllBillers()
    {
        return $this->getAllBillersBaseQuery()->get();
    }

    private function getAllBillersBaseQuery(): Builder
    {
        return $this->model->with(['user_detail', 'user_account']);
    }

    public function totalPayBills()
    {
        return $this->model->where('transaction_date', '<=', Carbon::now()->subDay())->where('status', '=', 'SUCCESS')->sum('total_amount');
    }

    public function totalamountPayBills()
    {
        return $this->model->where('transaction_date','<=',Carbon::now()->subDay())->where('status','=','SUCCESS')->sum('amount');
    }

    public function totalotherchargesPayBills()
    {
        return $this->model->where('transaction_date','<=',Carbon::now()->subDay())->where('status','=','SUCCESS')->sum('other_charges');
    }

    public function totalservicefeePayBills()
    {
        return $this->model->where('transaction_date','<=',Carbon::now()->subDay())->where('status','=','SUCCESS')->sum('service_fee');
    }

    public function reportData(string $from, string $to, string $filterBy = '', string $filterValue = '') {
        $records = BillerReport::where('original_transaction_date', '>=', $from)
        ->where('original_transaction_date', '<=', $to);

        if($filterValue != '' && $filterBy != '') {

            // IF CUSTOMER_ID
            if($filterBy == 'CUSTOMER_ID') {
                $records = $records->where('account_number', $filterValue);
            }
            // IF CUSTOMER_NAME
            else if ($filterBy == 'CUSTOMER_NAME') {
                $records = $records->where(function($q) use($filterValue) {
                    $q->where('first_name', 'LIKE', '%' . $filterValue . '%')
                      ->orWhere('last_name', 'LIKE', '%' . $filterValue . '%')
                      ->orWhere('middle_name', 'LIKE', '%' . $filterValue . '%');
                });
            }
            // IF STATUS
            else if($filterBy == 'STATUS') {
                $records = $records->where('status', $filterValue);
            }
            // IF REFERENCE NUMBER
            else if($filterBy == 'REFERENCE_NUMBER') {
                $records = $records->where('reference_number', $filterValue);
            }
            // IF BILLERS NAME
            else if($filterBy == 'BILLER_COMPANY') {
                $records = $records->where('billers_name', 'LIKE', '%' . $filterValue . '%');
            }

        }

        return $records->get();
    }
}

