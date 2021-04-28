<?php

namespace App\Services\Utilities\Notifications;


interface INotificationService
{
    public function sendLoginVerification(string $to, string $otp);

    public function sendAccountVerification(string $to, string $otp);

    public function sendPasswordVerification(string $to, string $otp, string $otpType);

    public function sendMoneyVerification(string $to, string $otp);

    public function sendMoneySenderNotification(string $to, array $fillRequest,string $receiverName);

    public function sendMoneyRecipientNotification(string $to, array $fillRequest, string $senderName);
}
