<?php

namespace App\Services\Utilities\Notifications\SMS;

use App\Services\Utilities\Notifications\INotificationService;

interface ISmsService extends INotificationService
{
    public function updateMobileVerification(string $to, string $otp, string $recipientName);
    public function sendLoanConfirmation(string $to, string $firstName, string $refNo);
    public function sendMerchantAccoutCredentials(string $to, string $firstName, string $password, string $pinCode);
    public function sendUserTransactionHistory(string $to, array $records);
}
