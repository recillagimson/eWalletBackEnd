<?php


namespace App\Services\Utilities\PrepaidLoad\ATM;


// use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;

interface IAtmService
{
    public function generateSignature(array $data): string;

    public function verifySignature(array $data, string $base64Signature);

    public function showNetworkAndPrefix(): array;

    public function atmload(array $items): array;

    public function showNetworkProuductList(array $items): array;

    public function convertMobileNumberPrefixToAreaCode(string $mobileNo): string;
}
