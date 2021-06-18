<?php

namespace App\Services\UserProfile;

use Carbon\Carbon;
use App\Enums\AccountTiers;
use App\Models\UserAccount;
use App\Enums\SuccessMessages;
use App\Traits\HasFileUploads;
use App\Enums\SquidPayModuleTypes;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Traits\Errors\WithUserErrors;
use App\Repositories\Tier\ITierRepository;
use Illuminate\Validation\ValidationException;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\Verification\IVerificationService;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserUtilities\Nationality\INationalityRepository;
use App\Repositories\UserUtilities\NatureOfWork\INatureOfWorkRepository;
use App\Repositories\UserUtilities\SourceOfFund\ISourceOfFundRepository;
use App\Repositories\UserUtilities\TempUserDetail\ITempUserDetailRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;

class UserProfileService implements IUserProfileService
{
    use HasFileUploads, WithUserErrors;
    
    public IUserAccountRepository $userAccountRepository;
    public IUserDetailRepository $userDetailRepository;
    public IUserPhotoRepository $userPhotoRepository;
    public ITempUserDetailRepository $tempUserDetail;
    public ITierRepository $tierRepository;
    private ITierApprovalRepository $userApprovalRepository;
    private IVerificationService $verificationService;
    private ILogHistoryService $logHistoryService;
    private IUserProfileService $userProfileService;


    public function __construct(IUserDetailRepository $userDetailRepository, 
                                IUserAccountRepository $userAccountRepository,
                                IUserPhotoRepository $userPhotoRepository,
                                ITempUserDetailRepository $tempUserDetail,
                                ITierRepository $tierRepository,
                                ITierApprovalRepository $userApprovalRepository,
                                IVerificationService $verificationService,
                                ILogHistoryService $logHistoryService,
                                IUserProfileService $userProfileService)
    {
        $this->userAccountRepository = $userAccountRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->userPhotoRepository = $userPhotoRepository;
        $this->tempUserDetail = $tempUserDetail;
        $this->tierRepository = $tierRepository;
        $this->userApprovalRepository = $userApprovalRepository;
        $this->verificationService = $verificationService;
        $this->logHistoryService = $logHistoryService;
        $this->userProfileService = $userProfileService;

    }

    public function update(object $userAccount, array $details) {
       $userProfile = $this->userDetailRepository->getByUserId($userAccount->id);
       $details["user_account_id"]= $userAccount->id;
       $details["user_account_status"]= $userAccount->status;
       $data = $this->addUserInput($details, $userAccount, $userProfile);

       $response = (!$userProfile) ? 
       $this->userDetailRepository->create($data)->toArray() : 
       $this->userDetailRepository->update($userProfile, $data);

        return (!$userProfile) ? $response : 
        $this->userDetailRepository->getByUserId($userAccount->id)->toArray();
    }

    public function addUserInput(array $details, object $userAccount, object $data=null) {

        if(!$data) {
            $details['user_created'] = $userAccount->id;
            $details['user_updated'] = $userAccount->id;
        }else {
            $details['user_updated'] = $userAccount->id;
        }
        return $details;
    }

    public function changeAvatar(array $data) {
        // Delete existing first
        // Get details first 
        $userDetails = $this->userDetailRepository->getByUserId(request()->user()->id);
        // If no user Details
        if(!$userDetails) {
            throw ValidationException::withMessages([
                'user_detail_not_found' => 'User Detail not found'
            ]);
        }
        // Delete file using path from current detail
        $this->deleteFile($userDetails->avatar_loction);

        // For Serfile Processing
        // GET EXT NAME
        $avatarPhotoExt = $this->getFileExtensionName($data['avatar_photo']);
        // GENERATE NEW FILE NAME
        $avatarPhotoName = request()->user()->id . "/" . \Str::random(40) . "." . $avatarPhotoExt;
        // PUT FILE TO STORAGE
        $avatarPhotoPath = $this->saveFile($data['avatar_photo'], $avatarPhotoName, 'avatar_photo');
        // SAVE AVATAR LOCATION ON USER DETAILS
        $record = $this->userPhotoRepository->updateAvatarPhoto($avatarPhotoPath);

        return $record;
        // return to controller all created records
    }

    public function updateUserProfile($id, array $request, object $user) 
    {
        $userAccount = $this->userAccountRepository->get($id);

        if(!$userAccount) {
            throw ValidationException::withMessages([
                'user_account_not_found' => 'User Account not found'
            ]);
        }
        
        $request = $this->addTransactionInfo($userAccount, $request, $user);
        $request = $this->addUserInput($request, $user);
        $request = array_filter($request);

        $dirty = $this->checkDirty($userAccount, $request);

        if ($dirty) {
            return [
                "status" => 1,
                "data" => $this->tempUserDetail->create($request)
            ];
        }

        $this->userDetailRepository->update($userAccount, $request);

        return [
            "status" => 0,
            "data" => $userAccount
        ];
    }

    public function supervisorUpdateUserProfile($id, array $request, object $user) 
    {
        $userAccount = $this->userAccountRepository->get($id);
        $userDetail = $user->profile;

        if(!$userAccount) {
            throw ValidationException::withMessages([
                'user_account_not_found' => 'User Account not found'
            ]);
        }

        if(!$userDetail) {
            throw ValidationException::withMessages([
                'user_detail_not_found' => 'User Detail not found'
            ]);
        }

        if ($request['tier_id']) {
            $this->validateTier($userAccount, $request['tier_id']);
        }
        
        $request = $this->addUserInput($request, $user, $userAccount);

        $this->userAccountRepository->update($userAccount, $request);
        $this->userDetailRepository->update($userDetail, $request);

        return [
            "status" => 0,
            "data" => $userAccount
        ];
    }

    public function addTransactionInfo(UserAccount $userAccount, array $request, object $user) 
    {
        $request['transaction_number'] = "PU" . Carbon::now()->format('YmdHi') . rand(0,99999);
        $request['reviewed_by'] = $user->id;
        $request['reviewed_date'] = Carbon::now();
        $request['user_account_id'] = $userAccount->id;
        $request['status'] = 0; //Pending

        return $request;
    }

    public function checkDirty(UserAccount $user, array $request) 
    {
        $userAccount = $user->fill($request);
        $userDetail = $user->profile->fill($request);

        if ($userAccount->isDirty('email')) {

            $email = $this->userAccountRepository->getByUsername('email', $userAccount->email);

            if ($email && $email->id != $userAccount->id) {
                $this->emailAlreadyTaken();
            }

            return true;
        }

        if ($userAccount->isDirty('mobile_number')) {

            $mobile = $this->userAccountRepository->getByUsername('mobile_number', $userAccount->mobile_number);

            if ($mobile && $mobile->id != $userAccount->id) {
                $this->mobileAlreadyTaken();
            }

            return true;
        }

        if ($userDetail->isDirty(['birth_date', 'first_name', 'last_name', 'middle_name'])) {
            return true;
        }

        return false;
    }

    public function validateTier(UserAccount $user, $tier_id) 
    {
        $tier = $this->tierRepository->get($user->tier_id);
        $reqTier = $this->tierRepository->get($tier_id);

        if(!$reqTier || !$tier) {
            throw ValidationException::withMessages([
                'tier_not_found' => 'Tier not found'
            ]);
        }

        $tierNum = intval(str_replace('Tier ', '', $tier->name)) + 1;
        $reqTierNum = intval(str_replace('Tier ', '', $reqTier->name));

        if ($tierNum != $reqTierNum) {
            throw ValidationException::withMessages([
                'tier_update_error' => 'Tier update invalid.'
            ]);
        }

        return true;
    }

    // public function upgradeFarmerToSilver(array $attr) {
    //     try {
    //         // GET USER ACCOUNT WITH TIER
    //         $user_account = $this->userAccountRepository->getUser($attr['user_account_id']);
    //         // IF REQUESTING FOR TIER UPDATE
    //         if($user_account && $user_account->tier->id !== AccountTiers::tier2) {
    //             // VALIDATE IF HAS EXISTING REQUEST
    //             $findExistingRequest = $this->userApprovalRepository->getPendingApprovalRequestByUserAccountId($attr['user_account_id']);
    //             if($findExistingRequest) {
    //                 return $this->tierUpgradeAlreadyExist();
    //             }

    //             // CREATE APPROVAL RECORD FOR ADMIN
    //             // TU-MMDDYYY-RANDON
    //             $generatedTransactionNumber = "TU" . Carbon::now()->format('YmdHi') . rand(0,99999);
    //             $tierApproval = $this->userApprovalRepository->updateOrCreateApprovalRequest([
    //                 'user_account_id' => request()->user()->id,
    //                 'request_tier_id' => AccountTiers::tier2,
    //                 'status' => 'PENDING',
    //                 'user_created' => request()->user()->id,
    //                 'user_updated' => request()->user()->id,
    //                 'transaction_number' => $generatedTransactionNumber
    //             ]);
    //             $this->verificationService->updateTierApprovalIds($attr['id_photos_ids'], $attr['id_selfie_ids'], $tierApproval->id);

    //             $audit_remarks = request()->user()->id . " has requested to upgrade to Silver";
    //             $this->logHistoryService->logUserHistory(request()->user()->id, "", SquidPayModuleTypes::upgradeToSilver, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);
    //         }
    //         // $details = $request->validated();
    //         $addOrUpdate = $this->userProfileService->update($user_account->profile(), $attr);
    //         $audit_remarks = request()->user()->id . " Profile Information has been successfully updated.";
    //         $this->logHistoryService->logUserHistory(request()->user()->id, "", SquidPayModuleTypes::updateProfile, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);

    //         // $encryptedResponse = $this->encryptionService->encrypt($addOrUpdate);
    //         return $this->responseService->successResponse($addOrUpdate, SuccessMessages::success);
    //     } catch(\Exception $e) {
    //         dd($e->getMessage());
    //     }
    // }
}
