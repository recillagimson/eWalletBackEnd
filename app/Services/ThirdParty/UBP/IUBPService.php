<?php

namespace App\Services\ThirdParty\UBP;

use Illuminate\Http\Client\Response;

interface IUBPService
{
    public function getBanks(string $provider): Response;

    public function fundTransfer(string $refNo, string $fromFullName, int $bankCode, string $recepientAccountNumber,
                                 string $recepientAccountName, float $amount, string $transactionDate,
                                 string $instructions, string $provider): Response;

    public function checkStatus(string $provider, string $refNo): Response;

    public function send2BankUBPDirect(string $senderRefId, string $transactionDate, string $accountNo, float $amount, string $remarks, string $particulars, string $recipientName) : Response;
    public function verifyPendingDirectTransaction(string $senderRefId);
    public function updateTransaction(string $status, string $remittanceId): Response;
}
