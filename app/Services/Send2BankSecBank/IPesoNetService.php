<?php
namespace App\Services\Send2BankSecBank;

interface IPesoNetService {
    public function validateTransaction(array $data, string $userId);
    public function transfer(array $data, string $userId);
}
