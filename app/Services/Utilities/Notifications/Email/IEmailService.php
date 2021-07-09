<?php

namespace App\Services\Utilities\Notifications\Email;

use App\Models\OutSend2Bank;
use App\Services\Utilities\Notifications\INotificationService;

interface IEmailService extends INotificationService
{
    public function sendSend2BankReceipt(string $to, OutSend2Bank $send2Bank);

    public function sendAdminUserAccountDetails(string $to, string $firtName, string $email, string $password);

    // public function tierUpgradeNotification(string $to, UserDetail $userDetail, Tier $tier);

    public function updateEmailVerification(string $to, string $otp);
}
