<?php


namespace App\Services\Utilities\PrepaidLoad\ATM;


use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;

interface IAtmService extends IPrepaidLoadService
{
    public function generateSignature(array $data): string;

    public function verifySignature(array $data, string $base64Signature);
}
