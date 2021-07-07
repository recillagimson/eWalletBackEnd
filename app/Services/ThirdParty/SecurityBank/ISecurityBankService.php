<?php


namespace App\Services\ThirdParty\SecurityBank;


use Illuminate\Http\Client\Response;

interface ISecurityBankService
{
    public function getBanks(string $provider): Response;

    public function fundTransfer(string $provider, array $data): Response;

    public function checkStatus(string $provider, string $traceNo): Response;
}
