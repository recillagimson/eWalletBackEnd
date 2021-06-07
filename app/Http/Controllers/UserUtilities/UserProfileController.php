<?php

namespace App\Http\Controllers\UserUtilities;

use App\Enums\AccountTiers;
use Illuminate\Http\Request;
use App\Http\Requests\UserProfile\UpdateProfileRequest;
use App\Http\Requests\UserProfile\AvatarUploadRequest;
use Illuminate\Http\Response;
use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Encryption\IEncryptionService;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\UserProfile\UpdateProfileBronzeRequest;
use App\Http\Requests\UserProfile\UpdateProfileSilverRequest;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\Utilities\Verification\IVerificationService;

class UserProfileController extends Controller
{

    private IEncryptionService $encryptionService;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;
    private IUserDetailRepository $userDetailRepository;
    private ITierApprovalRepository $userApprovalRepository;
    private IVerificationService $verificationService;

    public function __construct(IEncryptionService $encryptionService, 
                                IUserProfileService $userProfileService,
                                IResponseService $responseService,
                                ITierApprovalRepository $userApprovalRepository,
                                IUserDetailRepository $userDetailRepository,
                                IVerificationService $verificationService)
    {
        $this->encryptionService = $encryptionService;
        $this->userProfileService = $userProfileService;
        $this->responseService = $responseService;
        $this->userDetailRepository = $userDetailRepository;
        $this->userApprovalRepository = $userApprovalRepository;
        $this->verificationService = $verificationService;
    }

    /**
     * Add or Update for Bronze Users
     *
     * @param PrepaidLoadRequest $request
     * @return JsonResponse
     */
    public function updateBronze(UpdateProfileBronzeRequest $request): JsonResponse
    {
        $details = $request->validated();
        $addOrUpdate = $this->userProfileService->update($request->user(), $details);
        
        // $encryptedResponse = $this->encryptionService->encrypt($addOrUpdate);
        return $this->responseService->successResponse($addOrUpdate, SuccessMessages::success);
    }

    /**
     * Add or Update for Silver Upgrade Users
     *
     * @param PrepaidLoadRequest $request
     * @return JsonResponse
     */
    public function updateSilver(UpdateProfileSilverRequest $request): JsonResponse
    {
        // IF REQUESTING FOR TIER UPDATE
        if(request()->user() && request()->user()->tier && request()->user()->tier->id !== AccountTiers::tier2) {
            // CREATE APPROVAL RECORD FOR ADMIN
            $tierApproval = $this->userApprovalRepository->updateOrCreateApprovalRequest([
                'user_account_id' => request()->user()->id,
                'request_tier_id' => AccountTiers::tier2,
                'status' => 'PENDING',
                'user_created' => request()->user()->id,
                'user_updated' => request()->user()->id
            ]);
            $this->verificationService->updateTierApprovalIds($request->id_photos_ids, $request->id_selfie_ids, $tierApproval->id);
        }
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
        return ($user_detail) ? 
        $this->responseService->successResponse($user_detail->toArray(), SuccessMessages::success) :
        $this->responseService->notFound("No Data Found.");
    }

    public function changeAvatar(AvatarUploadRequest $request) 
    {
        $createRecord = $this->userProfileService->changeAvatar($request->validated());

        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::success);
    }

}
