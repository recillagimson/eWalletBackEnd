<?php


namespace App\Services\ThirdParty\DragonPay;


use Illuminate\Http\Client\Response;

interface IDragonPayService
{
    public function generateUrl(string $refNo, string $email, string $fullName, float $amount): Response;

    public function checkStatus(string $refNo): Response;
}
