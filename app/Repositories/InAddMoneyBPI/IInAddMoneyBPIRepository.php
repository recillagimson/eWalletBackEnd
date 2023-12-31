<?php

namespace App\Repositories\InAddMoneyBPI;

use App\Repositories\IRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface IInAddMoneyBPIRepository extends IRepository
{
    public function getSumOfTransactions(string $from, string $to, string $userId);
    public function getPromoTransaction(string $userId, Carbon $from, Carbon $to, float $minAmount, string $currentTransactionId): Collection;
}
