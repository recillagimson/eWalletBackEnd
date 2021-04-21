<?php

namespace App\Services\Send2Bank;

interface ISend2BankService
{
    /**
     * Returns supported banks of a specific fund transfer
     * service.
     *
     * @return array
     */
    public function getBanks(): array;

    public function fundTransfer(string $fromUserId, array $recipient);
}
