<?php


namespace App\Services\Utilities\Notifications;

use Carbon\Carbon;
use App\Models\Tier;
use App\Models\UserUtilities\UserDetail;

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


    public function payBillsNotification(string $to, array $fillRequest, string $biller)
    {
        // TODO: Implement payBillsNotification() method.
    }

    public function sendSend2BankSenderNotification(string $to, string $refNo, string $accountNo, float $amount, Carbon $transactionDate, float $serviceFee, float $newBalance, string $provider, string $remittanceId)
    {
        // TODO: Implement sendSend2BankSenderNotification() method.
    }

    public function buyLoadNotification(string $to, float $amount, string $productName, string $recipientMobileNumber, Carbon $transactionDate, float $newBalance, string $refNo)
    {
        // TODO: Implement buyLoadNotification() method.
    }

    public function tierUpgradeNotification(string $to, UserDetail $userDetail, Tier $tier) {
        // TODO: Implement tierUpgradeNotification() method.
    }
}
