<?php

namespace App\Services\Utilities\Notifications;


use App\Enums\OtpTypes;

interface INotificationService
{
    public function sendLoginVerification(string $to, string $otp);

    public function sendAccountVerification(string $to, string $otp);

    public function sendPasswordVerification(string $to, string $otp, string $otpType = OtpTypes::passwordRecovery);
}
