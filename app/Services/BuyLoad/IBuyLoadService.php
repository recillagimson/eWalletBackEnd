<?php


namespace App\Services\BuyLoad;


interface IBuyLoadService
{
    public function getEpinProducts(): array;

    public function getProductsByProvider(string $mobileNumber): array;

    public function validateTopup(string $userId, string $recipientMobileNumber, string $productCode, string $productName,
                                  float  $amount);

    public function topup(string $userId, string $recipientMobileNumber, string $productCode, string $productName,
                          float  $amount, string $type): array;

    public function processPending(string $userId): array;

    public function processAllPending();

    public function executeDisabledNetwork(string $mobileNumber);
}
