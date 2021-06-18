<?php

namespace App\Services\FarmerProfile;

use App\Enums\AccountTiers;
use Illuminate\Support\Str;
use App\Enums\SuccessMessages;
use App\Traits\HasFileUploads;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use App\Enums\SquidPayModuleTypes;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Support\Facades\Storage;
use App\Services\KYCService\IKYCService;
use App\Services\UserAccount\IUserAccountService;
use App\Services\UserProfile\IUserProfileService;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;
use App\Services\Utilities\Verification\IVerificationService;

class FarmerProfileService implements IFarmerProfileService
{
    use HasFileUploads, WithUserErrors;
    
    private ITierApprovalRepository $userApprovalRepository;
    private IVerificationService $verificationService;
    private ILogHistoryService $logHistoryService;
    private IUserProfileService $userProfileService;

    private IUserAccountRepository $userAccountRepository;
    private IKYCService $kycService;
    private IUserPhotoRepository $userPhotoRepository;
    private IUserSelfiePhotoRepository $userSelfieRepository;


    public function __construct(
                                ITierApprovalRepository $userApprovalRepository, IUserAccountRepository $userAccountRepository, IVerificationService $verificationService, ILogHistoryService $logHistoryService, IUserProfileService $userProfileService, IKYCService $kycService, IUserPhotoRepository $userPhotoRepository, IUserSelfiePhotoRepository $userSelfieRepository)
    {
        $this->userApprovalRepository = $userApprovalRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->verificationService = $verificationService;
        $this->logHistoryService = $logHistoryService;
        $this->userProfileService = $userProfileService;
        $this->userProfileService = $userProfileService;
        $this->userPhotoRepository = $userPhotoRepository;
        $this->kycService = $kycService;
        $this->userSelfieRepository = $userSelfieRepository;
    }

    public function upgradeFarmerToSilver(array $attr, string $authUser) {
        \DB::beginTransaction();
            // GET USER ACCOUNT WITH TIER
            $user_account = $this->userAccountRepository->getUser($attr['user_account_id']);
            // IF REQUESTING FOR TIER UPDATE
            if($user_account && $user_account->tier->id !== AccountTiers::tier2) {
                // VALIDATE IF HAS EXISTING REQUEST
                $findExistingRequest = $this->userApprovalRepository->getPendingApprovalRequestByUserAccountId($attr['user_account_id']);
                if($findExistingRequest) {
                    return $this->tierUpgradeAlreadyExist();
                }

                // init KYCC Face match
                $is_kyc_ai_approved = false;
                $kyc_response = null;
                foreach($attr['id_photos_ids'] as $idPhoto) {
                    // Get Entry for photo id
                    $userPhoto = $this->userPhotoRepository->get($idPhoto);
                    // Generate S3 Temp URL
                    $create_clone_file = Storage::disk('s3')->temporaryUrl($userPhoto->photo_location, Carbon::now()->addMinutes(10));
                    // Generate temp name for file from S3
                    $temp_image = tempnam(sys_get_temp_dir(), Str::random(10));
                    // Copy file to Tmp directory
                    copy($create_clone_file, $temp_image);
                    // REMOVE TEMP FILE
                    unset($temp_image);
                    
                    foreach($attr['id_selfie_ids'] as $selfiePhoto) {
                        $selfie = $this->userSelfieRepository->get($selfiePhoto);
                        // Generate S3 Temp Url
                        $create_selfie_clone_file = Storage::disk('s3')->temporaryUrl($selfie->photo_location, Carbon::now()->addMinutes(10));
                        // Generate Temp name for file catch from S3
                        $temp_selfie_image = tempnam(sys_get_temp_dir(), Str::random(10));
                        // Copy file to Tmp directory
                        copy($create_selfie_clone_file, $temp_selfie_image);
                        // Initialize KYC face match
                        $eKYCResponse = $this->kycService->initFaceMatch(['id_photo' => $temp_image, 'selfie_photo' => $temp_selfie_image], true);
                        // REMOVE TEMP FILE
                        unset($temp_selfie_image);
                    }   
                }

                // CREATE APPROVAL RECORD FOR ADMIN
                // TU-MMDDYYY-RANDON
                $generatedTransactionNumber = "TU" . Carbon::now()->format('YmdHi') . rand(0,99999);
                $tierApproval = $this->userApprovalRepository->updateOrCreateApprovalRequest([
                    'user_account_id' => $user_account->id,
                    'request_tier_id' => AccountTiers::tier2,
                    'status' => 'PENDING',
                    'user_created' => $authUser,
                    'user_updated' => $authUser,
                    'transaction_number' => $generatedTransactionNumber
                ]);
                $this->verificationService->updateTierApprovalIds($attr['id_photos_ids'], $attr['id_selfie_ids'], $tierApproval->id);

                $audit_remarks = $user_account->id . " has requested to upgrade to Silver";
                $record = $this->logHistoryService->logUserHistory($user_account->id, "", SquidPayModuleTypes::upgradeToSilver, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);
            }
            // $details = $request->validated();
            // dd($user_account->profile);
            $addOrUpdate = $this->userProfileService->update($user_account, $attr);
            $audit_remarks = $authUser . " Profile Information has been successfully updated.";
            $this->logHistoryService->logUserHistory($authUser, "", SquidPayModuleTypes::updateProfile, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);
            return $addOrUpdate;
    }
}
