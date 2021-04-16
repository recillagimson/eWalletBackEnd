<?php

namespace App\Services\Utilities\Notifications;


interface IPushNotificationService
{
    public function getByUserId(string $userId);
    public function create($params);
    public function update($notification, $params);
    public function delete($notification);
}
