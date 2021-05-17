<?php

namespace App\Repositories\OutBuyLoad;

use App\Models\OutBuyLoad;
use App\Repositories\IRepository;
use Carbon\Carbon;

interface IOutBuyLoadRepository extends IRepository
{
    public function getPending(string $userId);

    public function createTransaction(string $userId, string $refNo, string $productCode, string $productName,
                                      string $recipientMobileNumber, float $amount, Carbon $transactionDate,
                                      string $transactionCategoryId, string $userCreated): OutBuyLoad;
}
