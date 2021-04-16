<?php

namespace App\Services\FundTransfer;

use App\Repositories\FundTransfer\IOutSendToBankRepository;
use App\Repositories\FundTransfer\IUserBalanceInfoRepository;

interface ICashoutService
{
    public function cashout(array $newCashout);
}
