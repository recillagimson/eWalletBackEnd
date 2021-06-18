<?php

namespace App\Services\FarmerProfile;

use App\Enums\AccountTiers;
use App\Enums\SuccessMessages;
use App\Traits\HasFileUploads;
use Illuminate\Support\Carbon;
use App\Enums\SquidPayModuleTypes;
use App\Traits\Errors\WithUserErrors;
use App\Services\UserAccount\IUserAccountService;
use App\Services\UserProfile\IUserProfileService;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Verification\IVerificationService;

class FarmerProfileService implements IFarmerProfileService
{
    use HasFileUploads, WithUserErrors;
    
    private ITierApprovalRepository $userApprovalRepository;
    private IVerificationService $verificationService;
    private ILogHistoryService $logHistoryService;
    private IUserProfileService $userProfileService;

    private IUserAccountRepository $userAccountRepository;


    public function __construct(
                                ITierApprovalRepository $userApprovalRepository, IUserAccountRepository $userAccountRepository, IVerificationService $verificationService, ILogHistoryService $logHistoryService, IUserProfileService $userProfileService)
    {
        $this->userApprovalRepository = $userApprovalRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->verificationService = $verificationService;
        $this->logHistoryService = $logHistoryService;
        $this->userProfileService = $userProfileService;
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

                // init eKYC AI Verification
                $is_eky_approved = null;
                

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
