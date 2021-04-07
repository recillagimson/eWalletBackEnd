<?php

namespace App\Repositories\Notification;

use App\Models\Notification;
use App\Repositories\Repository;

class NotificationRepository extends Repository implements INotificationRepository
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    public function getNotification(string $notificationId)
    {
        return $this->model->where('notification_id', '=', $notificationId)->first();
    }
}
