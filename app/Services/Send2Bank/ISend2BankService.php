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
     * Fund transfer to recepient bank acccount
     *
     * @param string $fromUserId
     * @param array $recipient
     * @throws Throwable
     */
    public function fundTransfer(string $fromUserId, array $recipient);

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
}
