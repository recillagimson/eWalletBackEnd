<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;

interface ITransactionService
{
    // public function addAvailableBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance);
    // public function subtractAvailableBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance);
    // public function addPendingBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance);
    // public function subtractPendingBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance);
    public function addUserBalanceInfo(string $userAccountId, string $currentId, float $availableBalance, float $pendingBalance);
    public function createUserTransactionEntry(string $userAccountId, string $transactionId, string $referenceNumber, string $transactionCategoryId);
    public function getUserBalanceById(string $userAccountId);
}