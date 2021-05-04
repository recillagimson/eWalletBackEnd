<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;

interface ITransactionValidationService
{
    /**
     * Transactions validation logic
     *
     * @param UserAccount $user
     * @param string $transactionCategoryId
     * @param float $totalAmount
     * @return mixed
     */
    public function validate(UserAccount $user, string $transactionCategoryId, float $totalAmount);

    /**
     * Check if transaction exceeds total monthly limit of
     * user account.
     *
     * @param UserAccount $user
     * @param float $totalAmount
     * @return mixed
     */
    public function checkUserMonthlyTransactionLimit(UserAccount $user, float $totalAmount);

    /**
     * Check if transaction exceeds remaining user account
     * balance.
     *
     * @param UserAccount $user
     * @param $totalAmount
     * @return mixed
     */
    public function checkUserBalance(UserAccount $user, $totalAmount);

    public function validateUser(UserAccount $user);
}
