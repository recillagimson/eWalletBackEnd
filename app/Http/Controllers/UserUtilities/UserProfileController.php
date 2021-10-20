<?php

namespace App\Http\Controllers\UserUtilities;

use App\Enums\AccountTiers;
use App\Enums\SquidPayModuleTypes;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\SupervisorUpdateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\UserProfile\AvatarUploadRequest;
use App\Http\Requests\UserProfile\UpdateFarmerToSilverRequest;
use App\Http\Requests\UserProfile\UpdateProfileBronzeRequest;
use App\Http\Requests\UserProfile\UpdateProfileRequest;
use App\Http\Requests\UserProfile\UpdateProfileSilverRequest;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\Encryption\IEncryptionService;
use App\Services\KYCService\IKYCService;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\Utilities\Verification\IVerificationService;
use App\Traits\Errors\WithUserErrors;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\UserUtilities\UserDetail;
use App\Http\Requests\User\DAPersonelRequest;

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
    private IKYCService $kycService;
    private IUserAccountRepository $userAccountRepository;

    public function __construct(IEncryptionService      $encryptionService,
                                IUserProfileService     $userProfileService,
                                IResponseService        $responseService,
                                ITierApprovalRepository $userApprovalRepository,
                                IUserDetailRepository   $userDetailRepository,
                                IVerificationService    $verificationService,
                                ILogHistoryService      $logHistoryService,
                                IKYCService             $kycService,
                                IUserAccountRepository  $userAccountRepository)
    {
        $this->encryptionService = $encryptionService;
        $this->userProfileService = $userProfileService;
        $this->responseService = $responseService;
        $this->userDetailRepository = $userDetailRepository;
        $this->userApprovalRepository = $userApprovalRepository;
        $this->verificationService = $verificationService;
        $this->logHistoryService = $logHistoryService;
        $this->kycService = $kycService;
        $this->userAccountRepository = $userAccountRepository;
    }

    /**
     * Add or Update for Bronze Users
     *
     * @param PrepaidLoadRequest $request
     * @return JsonResponse
     */
    public function updateBronze(UpdateProfileBronzeRequest $request): JsonResponse
    {
        // $details = $request->validated();
        $addOrUpdate = $this->userProfileService->update($request->user(), $request->all());

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
        // dd(request()->user());
        $record = $this->userProfileService->upgradeToSilver($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
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

    public function addDAPersonel(DAPersonelRequest $request)
    {
        $details = $request->validated();
        $userdetail = $this->userDetailRepository->getByUserId($details["user_account_id"]); 

        $updateRecord = $this->userDetailRepository->update($userdetail, $details);
      
        return $this->responseService->successResponse(array($updateRecord), SuccessMessages::recordSaved);
    }

    public function getAvatarLinkByMobileNumber(string $mobileNumber) {
        $mobileNumber = $mobileNumber;
        $userDetail = $this->userAccountRepository->getAccountByMobileNumber($mobileNumber);
        $avatarLink = '';
        if($userDetail && isset($userDetail->profile)) {
            $avatarLink = $userDetail->profile->avatar_link;
            return $this->responseService->successResponse(['link' => $avatarLink], SuccessMessages::success);
        }

        // CHECK if email is used
        $userDetail = $this->userAccountRepository->getByEmail('email', $mobileNumber);
        if($userDetail && isset($userDetail->profile)) {
            $avatarLink = $userDetail->profile->avatar_link;
            return $this->responseService->successResponse(['link' => $avatarLink], SuccessMessages::success);
        }

        return $this->responseService->successResponse([], SuccessMessages::success);
    }

}
