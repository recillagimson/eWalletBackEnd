<?php

namespace App\Repositories\FundTransfer;

use App\Models\OutSendToBank;
use App\Repositories\IRepository;

interface IOutSendToBankRepository extends IRepository
{
    public function getRefNo();
}
