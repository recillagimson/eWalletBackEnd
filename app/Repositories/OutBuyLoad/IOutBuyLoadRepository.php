<?php

namespace App\Repositories\OutBuyLoad;

use App\Models\OutBuyLoad;
use App\Repositories\IRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface IOutBuyLoadRepository extends IRepository
{
    public function getPending(string $userId);

    public function getUsersWithPending();

    public function createTransaction(string $userId, string $refNo, string $productCode, string $productName,
                                      string $recipientMobileNumber, float $amount, Carbon $transactionDate,
                                      string $transactionCategoryId, string $type, string $userCreated): OutBuyLoad;

    public function getSumOfTransactions(string $from, string $to, string $userAccountId);

    public function totalBuyload();

    public function getSmartPromoTransaction(string $userId, int $month, int $year, string $currentTransactionId): Collection;

}
