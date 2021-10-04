<?php

namespace App\Repositories\QrTransactions;

use App\Repositories\Repository;
use App\Models\QrTransactions;
use App\Models\UserAccount;

class QrTransactionsRepository extends Repository implements IQrTransactionsRepository
{
    public function __construct(QrTransactions $model)
    {
        parent::__construct($model);
    }

   
    public function getQrWithZeroAmount(UserAccount $user)
    {
       return $this->model->where('amount', '=', 0)->where('user_account_id', '=', $user->id)->first();   
    }
 
}
