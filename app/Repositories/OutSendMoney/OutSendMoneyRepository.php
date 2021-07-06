<?php

namespace App\Repositories\OutSendMoney;

use App\Repositories\Repository;
use App\Models\OutSendMoney;

class OutSendMoneyRepository extends Repository implements IOutSendMoneyRepository
{
    public function __construct(OutSendMoney $model)
    {
        parent::__construct($model);
    }

    public function getLastRefNo()
    {
        return $this->model->orderByDesc('reference_number')->pluck('reference_number')->first();
    }

    public function getSumOfTransactions($from, $to, string $userAccountId) {
        return $this->model->where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to)
            ->where('status', '!=', 'FAILED')
            ->where('user_account_id', $userAccountId)
            ->sum('total_amount');
    }

    public function totalSendMoney()
    {
        return $this->model->where('transaction_date','<=',Carbon::now()->subDay())->where('status','=','successful')->sum('total_amount');
    }

    public function totalamountSendMoney()
    {
        return $this->model->where('transaction_date','<=',Carbon::now()->subDay())->where('status','=','successful')->sum('amount');
    }

    public function totalservicefeeSendMoney()
    {
        return $this->model->where('transaction_date','<=',Carbon::now()->subDay())->where('status','=','successful')->sum('service_fee');
    }

}   
