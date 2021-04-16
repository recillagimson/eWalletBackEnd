<?php

namespace App\Repositories\FundTransfer;

use App\Repositories\Repository;
use App\Models\OutSendToBank;

class OutSendToBankRepository extends Repository implements IOutSendToBankRepository
{
    public function __construct(OutSendToBank $model)
    {
        parent::__construct($model);
    }

    public function getRefNo()
    {
        $refno = $this->model->orderBy('reference_number', 'desc')->pluck('reference_number')->first();
        $refno = substr($refno, 2);
        $newRefno = $refno + 1;
        
        return ReferenceNumberTypes::SendToBank . str_pad($newRefno, 7, '0', STR_PAD_LEFT);
    }
}
