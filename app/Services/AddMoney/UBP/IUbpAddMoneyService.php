<?php

namespace App\Services\AddMoney\UBP;

use App\Models\InAddMoneyUbp;

interface IUbpAddMoneyService
{
    public function addMoney(string $userId, float $amount): InAddMoneyUbp;

    public function processPending(string $userId): array;
}
