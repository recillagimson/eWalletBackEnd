<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserProfile\UpdateProfileRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Enums\SuccessMessages;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;

class UserProfileController extends Controller
{
    private IEncryptionService $encryptionService;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;
    private IUserDetailRepository $userDetailRepository;

    public function __construct(IEncryptionService $encryptionService, 
                                IUserProfileService $userProfileService,
                                IResponseService $responseService,
                                IUserDetailRepository $userDetailRepository)
    {
        $this->encryptionService = $encryptionService;
        $this->userProfileService = $userProfileService;
        $this->responseService = $responseService;
        $this->userDetailRepository = $userDetailRepository;
    }

    /**
     * Add or Update
     *
     * @param PrepaidLoadRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $details = $request->validated();
        $addOrUpdate = $this->userProfileService->update($request->user(), $details);
        
        // $encryptedResponse = $this->encryptionService->encrypt($addOrUpdate);
        return $this->responseService->successResponse($addOrUpdate, SuccessMessages::success);
    }

    /**
     * Display the specified resource.
     *
     * @param  Model  $marital_status
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $user_detail = $this->userDetailRepository->getByUserId($request->user()->id);
        // $encryptedResponse = $this->encryptionService->encrypt($user_detail->toArray());
        return $this->responseService->successResponse($user_detail->toArray(), SuccessMessages::success);
    }

}
