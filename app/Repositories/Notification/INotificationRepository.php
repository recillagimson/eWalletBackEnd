<?php

namespace App\Repositories\Notification;

use App\Repositories\IRepository;

interface INotificationRepository extends IRepository
{
    public function getNotification(string $notificationId);
}
