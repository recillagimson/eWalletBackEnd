<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserUtilities\Nationality\INationalityRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserUtilities\NationalityRequest;
use App\Models\UserUtilities\Nationality;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Enums\SuccessMessages;

class NationalityController extends Controller
{

    private IEncryptionService $encryptionService;
    private INationalityRepository $nationalityRepository;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;
    
    public function __construct(INationalityRepository $nationalityRepository,
                                IEncryptionService $encryptionService,
                                IUserProfileService $userProfileService,
                                IResponseService $responseService)
    {
        $this->nationalityRepository = $nationalityRepository;
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
        $records = $this->nationalityRepository->getAll();

        // $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  NationalityRequest $request
     * @return JsonResponse
     */
    public function store(NationalityRequest $request): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user());
        $createRecord = $this->nationalityRepository->create($inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param  Nationality $nationality
     * @return JsonResponse
     */
    public function show(Nationality $nationality): JsonResponse
    {
        // $encryptedResponse = $this->encryptionService->encrypt($nationality->toArray());
        return $this->responseService->successResponse($nationality->toArray(), SuccessMessages::success);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  NationalityRequest $request
     * @param  Nationality $nationality
     * @return JsonResponse
     */
    public function update(NationalityRequest $request, Nationality $nationality): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user(), $nationality);
        $updateRecord = $this->nationalityRepository->update($nationality, $inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return $this->responseService->successResponse(array($updateRecord), SuccessMessages::recordSaved);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Nationality $nationality
     * @return JsonResponse
     */
    public function destroy(Nationality $nationality): JsonResponse
    {
        $deleteRecord = $this->nationalityRepository->delete($nationality);

        return $this->responseService->successResponse(null, SuccessMessages::recordDeleted);
    }
}
