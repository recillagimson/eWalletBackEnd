<?php
namespace App\Repositories\OutSendMoney;

use App\Repositories\OutSendMoney\OutSendMoneyRepository;

interface IOutSendMoneyRepository extends OutSendMoneyRepository
{
    public function getLastRefNo();
}

