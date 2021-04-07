<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\Notification\NotificationRequest;
use App\Repositories\Notification\INotificationRepository;

class NotificationController extends Controller
{
    private IEncryptionService $encryptionService;
    private INotificationRepository $newsAndUpdateRepository;

    public function __construct(INotificationRepository $iNotificationRepository,
                                IEncryptionService $encryptionService)
    {
        $this->iNotificationRepository = $iNotificationRepository;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Show List
     *
     * 
     * @return JsonResponse
     */
    public function GetAll(): JsonResponse {
        $records = $this->iNotificationRepository->getAll();
        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Create Record
     *
     * @param NotificationRequest $request
     * @return JsonResponse
     */
    public function create(NotificationRequest $request): JsonResponse {
        $details = $request->validated();
        // $inputBody = $this->inputBody($details);
        $details['id'] = rand(0, 1000);
        $createRecord = $this->iNotificationRepository->create($details);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Show Record
     *
     * @param Notification $news
     * @return JsonResponse
     */
    public function show(Notification $notification): JsonResponse {
        $encryptedResponse = $this->encryptionService->encrypt($notification->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update Record
     *
     * @param Notification $news
     * @param NotificationRequest $request
     * @return JsonResponse
     */
    public function update(Notification $notification, NotificationRequest $request): JsonResponse {
        $details = $request->validated();
        // $inputBody = $this->inputBody($details);
        $updateRecord = $this->iNotificationRepository->update($notification, $details);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

     /**
     * Delete Record
     *
     * @param string $id
     * @return JsonResponse
     */
    public function delete(Notification $notification): JsonResponse {
        $deleteRecord = $this->iNotificationRepository->delete($notification);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
