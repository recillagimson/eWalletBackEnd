<?php

namespace App\Services\Send2Bank;

use Throwable;

interface ISend2BankService
{
    /**
     * Returns supported banks of a specific fund transfer
     * service.
     *
     * @return array
     */
    public function getBanks(): array;

    /**
     * Returns purpose library for instapay only.
     *
     * @return array
     */
    public function getPurposes(): array;

    /**
     * Validates user qualification for fund transfer
     *
     * @param string $userId
     * @param array $recipient
     * @return array
     */
    public function validateFundTransfer(string $userId, array $recipient): array;

    /**
     * Fund transfer to recepient bank acccount
     *
     * @param string $userId
     * @param array $data
     * @throws Throwable
     */
    public function fundTransfer(string $userId, array $data);

    /**
     * Check updates on pending transactions and process them accordingly
     *
     * @param string $userId
     * @return array
     */
    public function processPending(string $userId): array;

    /**
     * Endpoint to manually update transaction status. For testing purposes only.
     *
     * @param string $status
     * @param string $refNo
     * @return mixed
     */
    public function updateTransaction(string $status, string $refNo);

    public function processAllPending();
}
