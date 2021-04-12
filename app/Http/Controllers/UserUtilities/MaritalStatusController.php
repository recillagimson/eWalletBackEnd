<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserUtilities\MaritalStatus\IMaritalStatusRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserUtilities\MaritalStatusRequest;
use App\Models\UserUtilities\MaritalStatus;

class MaritalStatusController extends Controller
{

    private IEncryptionService $encryptionService;
    private IMaritalStatusRepository $maritalStatusRepository;
    
    public function __construct(IMaritalStatusRepository $maritalStatusRepository,
                                IEncryptionService $encryptionService)
    {
        $this->maritalStatusRepository = $maritalStatusRepository;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $records = $this->maritalStatusRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function store(MaritalStatusRequest $request): JsonResponse
    {
        $details = $request->validated();
        $createRecord = $this->maritalStatusRepository->create($details);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Model  $marital_status
     * @return JsonResponse
     */
    public function show(MaritalStatus $marital_status): JsonResponse
    {
        $encryptedResponse = $this->encryptionService->encrypt($marital_status->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  MaritalStatusRequest  $request
     * @param  Model  $marital_status
     * @return JsonResponse
     */
    public function update(MaritalStatusRequest $request, MaritalStatus $marital_status): JsonResponse
    {
        $details = $request->validated();
        $updateRecord = $this->maritalStatusRepository->update($marital_status, $details);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Model  $marital_status
     * @return JsonResponse
     */
    public function destroy(MaritalStatus $marital_status): JsonResponse
    {
        $deleteRecord = $this->maritalStatusRepository->delete($marital_status);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
