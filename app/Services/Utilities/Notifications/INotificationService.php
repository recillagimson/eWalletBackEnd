<?php

namespace App\Services\Utilities\Notifications;


interface INotificationService
{
    public function sendLoginVerification(string $to, string $otp);
    public function sendAccountVerification(string $to, string $otp);
    public function sendPasswordVerification(string $to, string $otp);
    public function getByUserId(string $userId);

}
