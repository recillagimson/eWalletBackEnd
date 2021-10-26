<?php

namespace App\Repositories\QrTransactions;

use App\Repositories\Repository;
use App\Models\QrTransactions;

class QrTransactionsRepository extends Repository implements IQrTransactionsRepository
{
    public function __construct(QrTransactions $model)
    {
        parent::__construct($model);
    }


}
