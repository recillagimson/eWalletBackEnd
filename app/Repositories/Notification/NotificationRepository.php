<?php

namespace App\Repositories\Notification;

use App\Models\Notification;
use Illuminate\Http\Request;
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

    public function getNotifications(Request $request) {
        $notifications = Notification::with([]);
        if($request->has('user_account_id')) {
            $notifications = $notifications->where('user_account_id', $request->user_account_id);
        }

        return $notifications->get();
    }
}
