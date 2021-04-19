<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;

interface ITransactionValidationService
{
    public function transactionValidation(string $userAccountId, string $transactionCategoryId, $total_amount);
    public function checkUserStatus(string $userAccountId);
    public function checkUserLockStatus(string $userAccountId);
    public function getTransactionCategory(string $transactionCategoryId);
    public function checkUserTier(string $userAccountId);
    public function checkUserMonthlyTransactionLimit(string $userAccountId, $total_amount);
    public function checkUserBalance(string $userAccountId);
}