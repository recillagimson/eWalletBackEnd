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

    public function create($params) {
        return $this->notificationRepository->create($params);
    }

    public function update($notification, $params) {
        return $this->notificationRepository->update($notification, $params);
    }

    public function delete($notification){
        return $this->notificationRepository->delete($notification);
    }

}