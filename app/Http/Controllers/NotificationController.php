<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
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
}
