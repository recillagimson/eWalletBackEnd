<?php


namespace App\Services\Utilities\Notifications;

use Carbon\Carbon;

class NotificationService implements INotificationService
{
    public function __construct()
    {
    }

    public function sendLoginVerification(string $to, string $otp)
    {
    }

    public function sendAccountVerification(string $to, string $otp)
    {
    }

    public function sendPasswordVerification(string $to, string $otp, string $otpType)
    {
    }

    public function sendMoneyVerification(string $to, string $otp)
    {
        // TODO: Implement sendMoneyVerification() method.
    }

    public function updateEmailVerification(string $to, string $otp)
    {
        // TODO: Implement updateEmailVerification() method.
    }

    public function sendMoneySenderNotification(string $to, array $fillRequest, string $receiverName)
    {
        // TODO: Implement sendMoneySenderNotification() method.
    }

    public function sendMoneyRecipientNotification(string $to, array $fillRequest, string $senderName)
    {
        // TODO: Implement sendMoneyRecipientNotification() method.
    }

    public function sendSend2BankSenderNotification(string $to, string $refNo, string $accountNo, float $amount, Carbon $transactionDate, float $serviceFee, float $newBalance, string $provider, string $remittanceId)
    {
        // TODO: Implement sendSend2BankSenderNotification() method.
    }
}
