<?php

namespace App\Services\Send2Bank;

interface ISend2BankDirectService
{
    /**
     * Fund transfer to recepient bank acccount
     *
     * @param string $fromUserId
     * @param array $recipient
     * @param bool $requireOtp
     * @throws Throwable
     */
    // public function fundTransfer(string $fromUserId, array $recipient);
    public function fundTransferToUBPDirect(string $fromUserId, array $recipient, bool $requireOtp = true);
    public function verifyPendingDirectTransactions(string $userId): array;
    public function validateFundTransfer(string $userId, array $recipient);
}
