<?php


namespace App\Services\BuyLoad;


interface IBuyLoadService
{
    public function getProductsByProvider(string $mobileNumber): array;

    public function validateLoadTopup(string $userId, string $recipientMobileNumber, string $productCode, string $productName,
                                      float $amount);

    public function topupLoad(string $userId, string $recipientMobileNumber, string $productCode, string $productName,
                              float $amount): array;

    public function processPending(string $userId): array;
}
