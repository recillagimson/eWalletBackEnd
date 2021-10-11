<?php

namespace App\Repositories\InAddMoneyUpbDirect;

use App\Repositories\IRepository;
use Illuminate\Database\Eloquent\Collection;

interface IInAddMoneyUpbDirectRepository extends IRepository
{
    public function addMoney($data);

    public function addMoneyForMerchant($data, $user);

    public function merchantDetails($data, $user);

    public function saveTransactionDetails($data, $user, $referenceNumber, $ubpData);

}
