<?php

namespace App\Repositories\OutPayBills;

use App\Enums\TransactionStatuses;
use App\Models\BillerReport;
use App\Models\OutPayBills;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

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
        return $this->model->where([ 'user_account_id' => $userId, 'status' => TransactionStatuses::pending ])->get();
    }

    public function getAllBillersWithPaginate() {
        return $this->getAllBillersBaseQuery()->paginate();
    }

    public function getAllBillers() {
        return $this->getAllBillersBaseQuery()->get();
    }

    private function getAllBillersBaseQuery(): Builder {
        return $this->model->with(['user_detail', 'user_account']);
    }

    public function totalPayBills()
    {
        return $this->model->where('transaction_date','<=',Carbon::now()->subDay())->where('status','=','SUCCESS')->sum('total_amount');
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
        $records = BillerReport::where('transaction_date', '>=', $from)
        ->where('transaction_date', '<=', $to);

        // dump($from);
        // dd($to);
        // if($filterValue != '' && $filterBy != '') {
        //     $records = $records->where($filterBy, )
        // }

        return $records->get();
    }
}

