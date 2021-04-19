<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;

interface ITransactionValidationService
{
    public function transactionValidation(string $userAccountId, string $transactionCategoryId);
    public function checkUserStatus(string $userAccountId);
    public function checkUserLockStatus(string $userAccountId);
    public function checkTransaction(string $transactionCategoryId);
    public function checkUserTier(string $userAccountId);
    public function checkUserMonthlyTransactionLimit(string $userAccountId);
    public function checkUserBalance(string $userAccountId);
}