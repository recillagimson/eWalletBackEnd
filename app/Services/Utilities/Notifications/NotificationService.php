<?php


namespace App\Services\Utilities\Notifications;

use App\Repositories\Notification\INotificationRepository;

class NotificationService implements INotificationService
{

    private INotificationRepository $notificationRepository;


    public function __construct(INotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function sendPasswordVerification(string $to, string $otp){}

    public function getByUserId($userId) {
        return $notifications = $this->notificationRepository->getByUserId($userId);
    }

}