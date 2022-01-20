<?php

namespace App\Services\BPIService;

use App\Models\UserAccount;
use App\Services\BPIService\Models\BPIPromoNotifcation;

interface IBPINotificationService
{
    public function sendPromoSms(string $mobileNumber, BPIPromoNotifcation $params);

    public function sendPromoEmail(string $email, BPIPromoNotifcation $params);

    public function createPromoAppNotification(UserAccount $user, BPIPromoNotifcation $params);
}
