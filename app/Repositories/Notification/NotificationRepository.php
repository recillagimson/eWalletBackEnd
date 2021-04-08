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

    public function getByUserId(String $userId) {
        $notifications = Notification::where('user_account_id', $userId);
        return $notifications->get();
    }
}
