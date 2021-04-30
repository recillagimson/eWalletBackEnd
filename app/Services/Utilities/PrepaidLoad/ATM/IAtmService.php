<?php


namespace App\Services\Utilities\PrepaidLoad\ATM;


// use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;

interface IAtmService
{
    public function generateSignature(array $data): string;

    public function verifySignature(array $data, string $base64Signature);

    public function createATMPostBody(object $items=null): array;

    public function showNetworkAndPrefix(): array;

    public function atmload(object $items): array;
}
