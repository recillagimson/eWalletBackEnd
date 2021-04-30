<?php

namespace App\Services\ThirdParty\UBP;

use Illuminate\Http\Client\Response;

interface IUBPService
{
    public function getBanks(string $provider): Response;

    public function fundTransfer(string $refNo, string $fromFullName, int $bankCode, string $recepientAccountNumber,
                                 string $recepientAccountName, float $amount, string $transactionDate,
                                 string $instructions, string $provider): Response;

    public function checkStatus(string $provider, string $refNo): Response;
}
