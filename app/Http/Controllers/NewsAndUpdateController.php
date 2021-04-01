<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NewsAndUpdate\INewsAndUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\NewsAndUpdate\NewsAndUpdateRequest;

class NewsAndUpdateController extends Controller
{
    private IEncryptionService $encryptionService;
    private INewsAndUpdateService $newsAndUpdatesService;
    
    public function __construct(INewsAndUpdateService $newsAndUpdatesService,
                                IEncryptionService $encryptionService)
    {
        $this->newsAndUpdatesService = $newsAndUpdatesService;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Show List
     *
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        $records = $this->newsAndUpdatesService->index();

        $encryptedResponse = $this->encryptionService->encrypt(array($records));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Create Record
     *
     * @param NewsAndUpdateRequest $request
     * @return JsonResponse
     */
    public function createRecord(NewsAndUpdateRequest $request): JsonResponse {
        $details = $request->validated();
        $createRecord = $this->newsAndUpdatesService->createRecord($details);

        $encryptedResponse = $this->encryptionService->encrypt(array($createRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Show Record
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse {
        $showRecord = $this->newsAndUpdatesService->show($id);

        $encryptedResponse = $this->encryptionService->encrypt(array($showRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update Record
     *
     * @param string $id
     * @param NewsAndUpdateRequest $request
     * @return JsonResponse
     */
    public function update(string $id, NewsAndUpdateRequest $request): JsonResponse {
        $details = $request->validated();
        $updateRecord = $this->newsAndUpdatesService->update($id,$details);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Delete Record
     *
     * @param string $id
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse {
        $deleteRecord = $this->newsAndUpdatesService->delete($id);

        $encryptedResponse = $this->encryptionService->encrypt(array($deleteRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }
}
