<?php

namespace App\Services\PayBills;

use App\Models\UserAccount;

/**
 * @property
 * @property
 *
 */
interface IPayBillsService
{
    public function getBillers();

    public function getBillerInformation(string $billerCode);

    public function validateAccount(string $billerCode, string $accountNumber, $data, UserAccount $user);

    public function createPayment(string $billerCode, array $data, UserAccount $user);

    public function inquirePayment(string $billerCode, string $clientReference);

    public function getWalletBalance();

    public function processPending(UserAccount $user);

    public function processAllPending();

    public function downloadListOfBillersCSV();
}
