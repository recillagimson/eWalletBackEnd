<?php


namespace App\Services\Utilities\Notifications;

use App\Repositories\Notification\INotificationRepository;

class PushNotificationService implements IPushNotificationService
{

    private INotificationRepository $notificationRepository;


    public function __construct(INotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getByUserId($userId) {
        return $notifications = $this->notificationRepository->getByUserId($userId);
    }

}