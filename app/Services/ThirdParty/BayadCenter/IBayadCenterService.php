<?php

namespace App\Services\ThirdParty\BayadCenter;

use Illuminate\Http\Client\Response;

interface IBayadCenterService
{
    public function getBanks(string $provider): Response;

    public function fundTransfer(
        string $refNo,
        string $fromFullName,
        int $bankCode,
        string $recepientAccountNumber,
        string $recepientAccountName,
        float $amount,
        string $transactionDate,
        string $instructions,
        string $provider
    ): Response;

    public function checkStatus(string $provider, string $refNo): Response;

    public function updateTransaction(string $status, string $remittanceId): Response;
}
