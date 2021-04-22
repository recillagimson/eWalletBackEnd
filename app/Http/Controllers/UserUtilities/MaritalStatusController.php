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
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Enums\SuccessMessages;

class MaritalStatusController extends Controller
{

    private IEncryptionService $encryptionService;
    private IMaritalStatusRepository $maritalStatusRepository;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;
    
    public function __construct(IMaritalStatusRepository $maritalStatusRepository,
                                IEncryptionService $encryptionService,
                                IUserProfileService $userProfileService,
                                IResponseService $responseService)
    {
        $this->maritalStatusRepository = $maritalStatusRepository;
        $this->encryptionService = $encryptionService;
        $this->userProfileService = $userProfileService;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $records = $this->maritalStatusRepository->getAll();

        // $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
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
        $inputBody = $this->userProfileService->addUserInput($details, $request->user());
        $createRecord = $this->maritalStatusRepository->create($inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param  Model  $marital_status
     * @return JsonResponse
     */
    public function show(MaritalStatus $marital_status): JsonResponse
    {
        // $encryptedResponse = $this->encryptionService->encrypt($marital_status->toArray());
        return $this->responseService->successResponse($marital_status->toArray(), SuccessMessages::success);
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
        $inputBody = $this->userProfileService->addUserInput($details, $request->user(), $marital_status);
        $updateRecord = $this->maritalStatusRepository->update($marital_status, $inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return $this->responseService->successResponse(array($updateRecord), SuccessMessages::recordSaved);
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

        return $this->responseService->successResponse(null, SuccessMessages::recordDeleted);
    }
}
