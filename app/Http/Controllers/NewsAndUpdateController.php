<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\NewsAndUpdate\NewsAndUpdateRequest;
use App\Models\NewsAndUpdate;

class NewsAndUpdateController extends Controller
{
    private IEncryptionService $encryptionService;
    private INewsAndUpdateRepository $newsAndUpdateRepository;
    
    public function __construct(INewsAndUpdateRepository $newsAndUpdateRepository,
                                IEncryptionService $encryptionService)
    {
        $this->newsAndUpdateRepository = $newsAndUpdateRepository;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Show List
     *
     * 
     * @return JsonResponse
     */
    public function GetAll(): JsonResponse {
        $records = $this->newsAndUpdateRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Create Record
     *
     * @param NewsAndUpdateRequest $request
     * @return JsonResponse
     */
    public function create(NewsAndUpdateRequest $request): JsonResponse {
        $details = $request->validated();
        $inputBody = $this->inputBody($details);
        $createRecord = $this->newsAndUpdateRepository->create($inputBody);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Show Record
     *
     * @param NewsAndUpdate $news
     * @return JsonResponse
     */
    public function show(NewsAndUpdate $news): JsonResponse {
        $encryptedResponse = $this->encryptionService->encrypt($news->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update Record
     *
     * @param NewsAndUpdate $news
     * @param NewsAndUpdateRequest $request
     * @return JsonResponse
     */
    public function update(NewsAndUpdate $news, NewsAndUpdateRequest $request): JsonResponse {
        $details = $request->validated();
        $inputBody = $this->inputBody($details);
        $updateRecord = $this->newsAndUpdateRepository->update($news, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Delete Record
     *
     * @param string $id
     * @return JsonResponse
     */
    public function delete(NewsAndUpdate $news): JsonResponse {
        $deleteRecord = $this->newsAndUpdateRepository->delete($news);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function inputBody(array $details): array {
        $body = array(
                    'title'=>$details['title'],
                    'description'=>$details['description'],
                    'status'=>$details['status'] === 0 ? 0 : 1,
                    'image_location'=>$details['image_location'],
                );
        return $body;
    }
}
