<?php

namespace App\Http\Controllers\UserUtilities;

use App\Enums\AccountTiers;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\SuccessMessages;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Enums\SquidPayModuleTypes;
use App\Http\Controllers\Controller;
use App\Traits\Errors\WithUserErrors;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\Encryption\IEncryptionService;
use App\Services\UserProfile\IUserProfileService;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Http\Requests\UserProfile\AvatarUploadRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\User\SupervisorUpdateUserRequest;
use App\Http\Requests\UserProfile\UpdateProfileRequest;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Http\Requests\UserProfile\UpdateProfileBronzeRequest;
use App\Http\Requests\UserProfile\UpdateProfileSilverRequest;
use App\Services\Utilities\Verification\IVerificationService;
use App\Http\Requests\UserProfile\UpdateFarmerToSilverRequest;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;

class UserProfileController extends Controller
{

    use WithUserErrors;

    private IEncryptionService $encryptionService;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;
    private IUserDetailRepository $userDetailRepository;
    private ITierApprovalRepository $userApprovalRepository;
    private IVerificationService $verificationService;
    private ILogHistoryService $logHistoryService;

    public function __construct(IEncryptionService $encryptionService, 
                                IUserProfileService $userProfileService,
                                IResponseService $responseService,
                                ITierApprovalRepository $userApprovalRepository,
                                IUserDetailRepository $userDetailRepository,
                                IVerificationService $verificationService,
                                ILogHistoryService $logHistoryService)
    {
        $this->encryptionService = $encryptionService;
        $this->userProfileService = $userProfileService;
        $this->responseService = $responseService;
        $this->userDetailRepository = $userDetailRepository;
        $this->userApprovalRepository = $userApprovalRepository;
        $this->verificationService = $verificationService;
        $this->logHistoryService = $logHistoryService;
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

        $audit_remarks = request()->user()->account_number . "  has updated his/her profile";
        $this->logHistoryService->logUserHistory(request()->user()->id, "", SquidPayModuleTypes::upgradeToBronze, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);

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
            // VALIDATE IF HAS EXISTING REQUEST
            $findExistingRequest = $this->userApprovalRepository->getPendingApprovalRequest();
            if($findExistingRequest) {
                return $this->tierUpgradeAlreadyExist();
            }

            // CREATE APPROVAL RECORD FOR ADMIN
            // TU-MMDDYYY-RANDON
            $generatedTransactionNumber = "TU" . Carbon::now()->format('YmdHi') . rand(0,99999);
            $tierApproval = $this->userApprovalRepository->updateOrCreateApprovalRequest([
                'user_account_id' => request()->user()->id,
                'request_tier_id' => AccountTiers::tier2,
                'status' => 'PENDING',
                'user_created' => request()->user()->id,
                'user_updated' => request()->user()->id,
                'transaction_number' => $generatedTransactionNumber
            ]);
            $this->verificationService->updateTierApprovalIds($request->id_photos_ids, $request->id_selfie_ids, $tierApproval->id);

            $audit_remarks = request()->user()->account_number . " has requested to upgrade to Silver";
            $this->logHistoryService->logUserHistory(request()->user()->id, "", SquidPayModuleTypes::upgradeToSilver, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);
        }
        $details = $request->validated();
        $addOrUpdate = $this->userProfileService->update($request->user(), $details);
        $audit_remarks = request()->user()->account_number . " Profile Information has been successfully updated.";
        $this->logHistoryService->logUserHistory(request()->user()->id, "", SquidPayModuleTypes::updateProfile, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);

        // $encryptedResponse = $this->encryptionService->encrypt($addOrUpdate);
        return $this->responseService->successResponse($addOrUpdate, SuccessMessages::success);
    }

    public function updateSilverValidation(UpdateProfileSilverRequest $request): JsonResponse {
        // IF REQUESTING FOR TIER UPDATE
        if(request()->user() && request()->user()->tier && request()->user()->tier->id !== AccountTiers::tier2) {
            // VALIDATE IF HAS EXISTING REQUEST
            $findExistingRequest = $this->userApprovalRepository->getPendingApprovalRequest();
            if($findExistingRequest) {
                return $this->tierUpgradeAlreadyExist();
            }
        }
        return $this->responseService->successResponse([
            'id' => request()->user()->id
        ], SuccessMessages::success);
    }

    public function checkPendingTierUpgrate(): JsonResponse {
        // IF REQUESTING FOR TIER UPDATE
        if(request()->user() && request()->user()->tier && request()->user()->tier->id !== AccountTiers::tier2) {
            // VALIDATE IF HAS EXISTING REQUEST
            $findExistingRequest = $this->userApprovalRepository->getPendingApprovalRequest();
            if($findExistingRequest) {
                return $this->tierUpgradeAlreadyExist();
            }
        }
        return $this->responseService->successResponse([
            'id' => request()->user()->id
        ], SuccessMessages::success);
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

    public function updateProfile($id, UpdateUserRequest $request)
    {
        $fillRequest = $request->all();

        $review = $this->userProfileService->updateUserProfile($id, $fillRequest, $request->user());

        if ($review['status']) {
            $message = SuccessMessages::updateUserPending;
        } else {
            $message = SuccessMessages::updateUserSuccessful;
        }
        

        return $this->responseService->successResponse($review['data']->toArray(), $message);
    }

    public function supervisorUpdateProfile($id, SupervisorUpdateUserRequest $request)
    {
        $fillRequest = $request->all();

        $review = $this->userProfileService->supervisorUpdateUserProfile($id, $fillRequest, $request->user());

        $message = SuccessMessages::updateUserSuccessful;

        return $this->responseService->successResponse($review['data']->toArray(), $message);
    }

    public function updateFarmerToSilver(UpdateFarmerToSilverRequest $request)
    {
        // dd($request->all());
        // $record = $this->userProfileService->upgradeFarmerToSilver($request->all());
        // return $this->responseService->successResponse($record, SuccessMessages::updateUserSuccessful);
    }

}
