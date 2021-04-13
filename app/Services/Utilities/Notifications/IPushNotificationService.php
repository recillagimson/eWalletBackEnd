<?php

namespace App\Services\Utilities\Notifications;


interface IPushNotificationService
{
    public function getByUserId(string $userId);
}
