<?php

namespace App\Services\Utilities\Notifications;


interface INotificationService
{
    public function sendPasswordVerification(string $to, string $otp);
}
