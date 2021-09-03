<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;

interface ITransactionService
{
    public function processUserPending(UserAccount $user);

    public function processAllPending();

    public function addUserBalanceInfo(string $userAccountId, string $currencyId, float $availableBalance, float $pendingBalance);

    public function createUserTransactionEntry(string $userAccountId, string $transactionId, string $referenceNumber, string $transactionCategoryId);

    public function createUserTransactionEntryUnauthenticated(string $userAccountId, string $transactionId, string $referenceNumber, float $total_amount, string $transactionCategoryId);

    public function generateTransactionHistory(string $userAccountId, string $dateFrom, string $dateTo);

    public function downloadCountTotalAmountEachUserCSV(object $request);

    public function getTransactionHistoryAdmin(array $attr, bool $paginated = true);
}
