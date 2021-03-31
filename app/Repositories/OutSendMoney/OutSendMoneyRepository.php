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

    public function getSenderBalance(string $userID)
    {
        
        return 10000;
    }

    public function getReceiverBalance(string $userID)
    {
        return $dummyBalance;
    }

}
