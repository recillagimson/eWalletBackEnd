<?php

namespace App\Services\Utilities\Notifications;


use App\Models\Tier;
use App\Models\UserUtilities\UserDetail;
use Carbon\Carbon;

interface INotificationService
{
    public function sendLoginVerification(string $to, string $otp, string $recipientName);

    public function sendAccountVerification(string $to, string $otp, string $recipientName);

    public function sendPasswordVerification(string $to, string $otp, string $otpType, string $recipientName);

    public function sendMoneyVerification(string $to, string $otp, string $recipientName);

    public function sendS2BVerification(string $to, string $otp, string $recipientName);

    public function updateProfileVerification(string $to, string $otp, string $recipientName);

    public function sendMoneySenderNotification(string $to, array $fillRequest, string $receiverName);

    public function sendMoneyRecipientNotification(string $to, array $fillRequest, string $senderName);

    public function payBillsNotification(string $to, array $fillRequest, string $biller);

    public function sendSend2BankSenderNotification(string $to, string $refNo, string $accountNo, float $amount,
                                                    Carbon $transactionDate, float $serviceFee, float $newBalance,
                                                    string $provider, string $remittanceId);

    public function buyLoadNotification(string $to, float $amount, string $productName, string $recipientMobileNumber,
                                        Carbon $transactionDate, float $newBalance, string $refNo);

    public function tierUpgradeNotification(string $to, UserDetail $userDetail, Tier $tier);
}
