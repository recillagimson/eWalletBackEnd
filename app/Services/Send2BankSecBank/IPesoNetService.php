<?php
namespace App\Services\Send2BankSecBank;

interface IPesoNetService {
    public function validateTransaction(array $data, string $userId);
}
