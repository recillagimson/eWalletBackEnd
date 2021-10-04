<?php

namespace App\Repositories\QrTransactions;

use App\Models\UserAccount;
use App\Repositories\IRepository;

interface IQrTransactionsRepository extends IRepository
{
    public function getQrWithZeroAmount(UserAccount $user);
}
