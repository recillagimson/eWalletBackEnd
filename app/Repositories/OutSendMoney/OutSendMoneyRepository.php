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

}
