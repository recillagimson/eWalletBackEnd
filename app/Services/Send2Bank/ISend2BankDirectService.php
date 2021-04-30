<?php

namespace App\Services\Send2Bank;

interface ISend2BankDirectService
{
    /**
     * Fund transfer to recepient bank acccount
     *
     * @param string $fromUserId
     * @param array $recipient
     * @throws Throwable
     */
    // public function fundTransfer(string $fromUserId, array $recipient);
    public function fundTransferToUBPDirect(string $fromUserId, array $recipient, bool $requireOtp = true);
}
