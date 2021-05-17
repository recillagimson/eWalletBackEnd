<?php


namespace App\Services\Utilities\PrepaidLoad\ATM;


// use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

interface IAtmService
{
    public function generateSignature(array $data): string;

    public function verifySignature(array $data, string $base64Signature);

    public function getProvider(string $mobileNumber);

    public function getProductsByProvider(string $provider): Collection;

    public function topupLoad(string $productCode, string $mobileNumber, string $refNo): Response;

    public function checkStatus(string $refNo): Response;

    public function convertMobileNumberPrefixToAreaCode(string $mobileNo): string;
}
