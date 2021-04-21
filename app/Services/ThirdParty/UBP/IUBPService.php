<?php

namespace App\Services\ThirdParty\UBP;

use Illuminate\Http\Client\Response;

interface IUBPService
{
    public function getPesonetBanks(): Response;

    public function pesonetFundTransfer(string $refNo, string $fromFullName, int $bankCode, string $recepientAccountNumber,
                                        string $recepientAccountName, float $amount, string $transactionDate,
                                        string $instructions): Response;
}
