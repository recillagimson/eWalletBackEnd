<?php

namespace App\Services\ThirdParty\ECPay;

use Illuminate\Http\Client\Response;

interface IECPayService
{
    public function getBanks(): Response;

    // public function fundTransfer(string $provider, array $data): Response;

    // public function checkStatus(string $provider, string $traceNo): Response;
}
