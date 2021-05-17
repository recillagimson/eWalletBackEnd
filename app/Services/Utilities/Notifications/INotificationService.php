<?php

namespace App\Services\Utilities\Notifications;


use Carbon\Carbon;

interface INotificationService
{
    public function sendLoginVerification(string $to, string $otp);

    public function sendAccountVerification(string $to, string $otp);

    public function sendPasswordVerification(string $to, string $otp, string $otpType);

    public function sendMoneyVerification(string $to, string $otp);

    public function updateEmailVerification(string $to, string $otp);

    public function sendMoneySenderNotification(string $to, array $fillRequest, string $receiverName);

    public function sendMoneyRecipientNotification(string $to, array $fillRequest, string $senderName);

    public function sendSend2BankSenderNotification(string $to, string $refNo, string $accountNo, float $amount,
                                                    Carbon $transactionDate, float $serviceFee, float $newBalance,
                                                    string $provider, string $remittanceId);

    public function buyLoadNotification(string $to, float $amount, string $productName, string $recipientMobileNumber,
                                        Carbon $transactionDate, float $newBalance, string $refNo);
}
