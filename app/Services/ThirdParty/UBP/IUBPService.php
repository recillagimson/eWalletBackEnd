<?php

namespace App\Services\ThirdParty\UBP;

use Illuminate\Http\Client\Response;

interface IUBPService
{
    public function getBanks(string $provider): Response;

    public function getPurposes(): Response;

    public function fundTransfer(string $refNo, string $fromFullName, string $zipCode, int $bankCode, string $recipientAccountNumber,
                                 string $recipientAccountName, float $amount, string $transactionDate,
                                 string $instructions, string $provider, string $purpose = "1003"): Response;

    public function checkStatus(string $provider, string $refNo): Response;

    public function send2BankUBPDirect(string $senderRefId, string $transactionDate, string $accountNo, float $amount, string $remarks, string $particulars, string $recipientName): Response;

    public function verifyPendingDirectTransaction(string $senderRefId);

    public function updateTransaction(string $status, string $remittanceId): Response;

    public function generateAuthorizeUrl(): string;

    public function generateAccountToken(string $userId, string $code): array;

    public function merchantPayment(string $userToken, array $data): Response;

    public function checkMerchantPaymentStatus(string $refNo): Response;
}
