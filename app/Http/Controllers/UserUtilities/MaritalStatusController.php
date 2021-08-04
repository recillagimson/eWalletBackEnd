<?php

namespace App\Http\Controllers\UserUtilities;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserUtilities\MaritalStatusRequest;
use App\Models\UserUtilities\MaritalStatus;
use App\Repositories\UserUtilities\MaritalStatus\IMaritalStatusRepository;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;

class MaritalStatusController extends Controller
{

    private IMaritalStatusRepository $maritalStatusRepository;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;

    public function __construct(IMaritalStatusRepository $maritalStatusRepository,
                                IUserProfileService $userProfileService,
                                IResponseService $responseService)
    {
        $this->maritalStatusRepository = $maritalStatusRepository;
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
        $records = $this->maritalStatusRepository->getMaritalStatuses();

        // $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MaritalStatusRequest $request
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
     * @param MaritalStatus $marital_status
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
     * @param MaritalStatusRequest $request
     * @param MaritalStatus $marital_status
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
     * @param MaritalStatus $marital_status
     * @return JsonResponse
     */
    public function destroy(MaritalStatus $marital_status): JsonResponse
    {
        $this->maritalStatusRepository->delete($marital_status);

        return $this->responseService->successResponse(null, SuccessMessages::recordDeleted);
    }
}
