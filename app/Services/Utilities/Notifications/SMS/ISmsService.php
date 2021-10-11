<?php

namespace App\Services\Utilities\Notifications\SMS;

use App\Services\Utilities\Notifications\INotificationService;

interface ISmsService extends INotificationService
{
    public function updateMobileVerification(string $to, string $otp);
}
