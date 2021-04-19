<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\Notification\NotificationRequest;
use App\Services\Utilities\Notifications\IPushNotificationService;

class NotificationController extends Controller
{
    private IEncryptionService $encryptionService;
    private IPushNotificationService $iPushNotificationService;

    public function __construct(IPushNotificationService $iPushNotificationService,
                                IEncryptionService $encryptionService)
    {
        // $this->iNotificationRepository = $iNotificationRepository;
        $this->encryptionService = $encryptionService;
        $this->iPushNotificationService = $iPushNotificationService;
    }

    /**
     * Show List
     *
     * 
     * @return JsonResponse
     */
    public function GetAll(): JsonResponse {
        $records = $this->iPushNotificationService->getByUserId(request()->user()->id);
        // dd(request()->user());
        dd(\Auth::user());
        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Store Record
     *
     * @param NotificationRequest $request
     * @return JsonResponse
     */
    public function store(NotificationRequest $request): JsonResponse {
        $details = $request->validated();
        $details['user_created'] = request()->user()->id;
        $details['user_updated'] = request()->user()->id;
        $createRecord = $this->iPushNotificationService->create($details);

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
        $updateRecord = $this->iPushNotificationService->update($notification, $details);
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
        $deleteRecord = $this->iPushNotificationService->delete($notification);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
