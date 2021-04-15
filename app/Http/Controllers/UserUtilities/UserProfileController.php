<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserProfile\UpdateProfileRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
use App\Services\UserProfile\IUserProfileService;
use App\Models\UserUtilities\UserDetail;

class UserProfileController extends Controller
{
    private IEncryptionService $encryptionService;
    private IUserProfileService $userProfileService;

    public function __construct(IEncryptionService $encryptionService, 
                                IUserProfileService $userProfileService)
    {
        $this->encryptionService = $encryptionService;
        $this->userProfileService = $userProfileService;
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
        
        $encryptedResponse = $this->encryptionService->encrypt($addOrUpdate);
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  Model  $marital_status
     * @return JsonResponse
     */
    public function show(UserDetail $user_detail): JsonResponse
    {
        $encryptedResponse = $this->encryptionService->encrypt($user_detail->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

}
