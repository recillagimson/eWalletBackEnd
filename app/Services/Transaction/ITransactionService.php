<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;

interface ITransactionService
{
    public function addAvailableBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance);
    public function subtractAvailableBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance);
    public function addPendingBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance);
    public function subtractPendingBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance);
}