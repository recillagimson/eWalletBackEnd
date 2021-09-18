<?php

namespace App\Services\FarmerProfile;

use App\Enums\AccountTiers;
use App\Enums\eKYC;
use App\Enums\SquidPayModuleTypes;
use App\Exports\Farmer\Export\FailedExport;
use App\Exports\Farmer\Export\SuccessExport;
use App\Exports\Farmer\FailedUploadExport;
use App\Exports\Farmer\SubsidyFailedUploadExport;
use App\Exports\Farmer\SubsidySuccessUploadExport;
use App\Exports\Farmer\SuccessUploadExport;
use App\Imports\Farmers\FarmerAccountImport;
use App\Imports\Farmers\FarmersImport;
use App\Imports\Farmers\SubsidyImport;
use App\Repositories\InReceiveFromDBP\IInReceiveFromDBPRepository;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserAccountNumber\IUserAccountNumberRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\UserUtilities\MaritalStatus\IMaritalStatusRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\KYCService\IKYCService;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Services\Utilities\Verification\IVerificationService;
use App\Traits\Errors\WithUserErrors;
use App\Traits\HasFileUploads;
use DB;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FarmerProfileService implements IFarmerProfileService
{
    use HasFileUploads, WithUserErrors;

    private ITierApprovalRepository $userApprovalRepository;
    private IVerificationService $verificationService;
    private ILogHistoryService $logHistoryService;
    private IUserProfileService $userProfileService;
    private IUserAccountNumberRepository $userAccountNumbers;
    private IMaritalStatusRepository $maritalStatus;
    private IUserDetailRepository $userDetail;

    private IUserAccountRepository $userAccountRepository;
    private IUserDetailRepository $userDetailRepository;
    private IUserBalanceInfoRepository $userBalanceInfo;
    private IKYCService $kycService;
    private IUserPhotoRepository $userPhotoRepository;
    private IUserSelfiePhotoRepository $userSelfieRepository;
    private IInReceiveFromDBPRepository $inReceiveFromDBP;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private IReferenceNumberService $referenceNumberService;
    private IEmailService $emailService;


    public function __construct(
        ITierApprovalRepository           $userApprovalRepository,
        IUserAccountRepository            $userAccountRepository,
        IUserDetailRepository             $userDetailRepository,
        IUserBalanceInfoRepository        $userBalanceInfo,
        IVerificationService              $verificationService,
        ILogHistoryService                $logHistoryService,
        IUserProfileService               $userProfileService,
        IKYCService                       $kycService,
        IUserPhotoRepository              $userPhotoRepository,
        IUserSelfiePhotoRepository        $userSelfieRepository,
        IUserAccountNumberRepository      $userAccountNumbers,
        IMaritalStatusRepository          $maritalStatus,
        IInReceiveFromDBPRepository       $inReceiveFromDBP,
        IUserTransactionHistoryRepository $userTransactionHistoryRepository,
        IReferenceNumberService           $referenceNumberService,
        IEmailService                     $emailService,
        IUserDetailRepository             $userDetail
    )
    {
        $this->userApprovalRepository = $userApprovalRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->verificationService = $verificationService;
        $this->logHistoryService = $logHistoryService;
        $this->userProfileService = $userProfileService;
        $this->userProfileService = $userProfileService;
        $this->userPhotoRepository = $userPhotoRepository;
        $this->kycService = $kycService;
        $this->userSelfieRepository = $userSelfieRepository;
        $this->userAccountNumbers = $userAccountNumbers;
        $this->maritalStatus = $maritalStatus;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->inReceiveFromDBP = $inReceiveFromDBP;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->emailService = $emailService;
        $this->userDetail = $userDetail;
    }

    public function upgradeFarmerToSilver(array $attr, string $authUser) {
        DB::beginTransaction();
        try {
            // GET USER ACCOUNT WITH TIER
            $user_account = $this->userAccountRepository->getUser($attr['user_account_id']);
            // IF REQUESTING FOR TIER UPDATE
            // if($user_account && $user_account->tier->id !== AccountTiers::tier2) { //COMMENT FOR TESTING PURPOSES
                // VALIDATE IF HAS EXISTING REQUEST
                // $findExistingRequest = $this->userApprovalRepository->getPendingApprovalRequestByUserAccountId($attr['user_account_id']); //COMMENT FOR TESTING PURPOSES
                // if($findExistingRequest) { //COMMENT FOR TESTING PURPOSES
                    // return $this->tierUpgradeAlreadyExist(); //COMMENT FOR TESTING PURPOSES
                // } //COMMENT FOR TESTING PURPOSES

                // CREATE APPROVAL RECORD FOR ADMIN
                // TU-MMDDYYY-RANDON
                $generatedTransactionNumber = "TU" . Carbon::now()->format('YmdHi') . rand(0,99999);
                $tierApproval = $this->userApprovalRepository->updateOrCreateApprovalRequest([
                    'user_account_id' => $user_account->id,
                    'request_tier_id' => AccountTiers::tier2,
                    'status' => 'APPROVED',
                    'user_created' => $authUser,
                    'user_updated' => $authUser,
                    'transaction_number' => $generatedTransactionNumber,
                    'approved_by' => eKYC::eKYC,
                    'remarks' => eKYC::eKYC_remarks,
                    'approved_date' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
                $this->verificationService->updateTierApprovalIds($attr['id_photos_ids'], $attr['id_selfie_ids'], $tierApproval->id, true);
                $audit_remarks = $user_account->id . " has requested to upgrade to Silver";
                $record = $this->logHistoryService->logUserHistory($user_account->id, "", SquidPayModuleTypes::upgradeToSilver, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);
                $this->userAccountRepository->update($user_account, ['tier_id' => AccountTiers::tier2]);
            // } //COMMENT FOR TESTING PURPOSES
            // $details = $request->validated();
            // dd($user_account->profile);

            $this->userAccountRepository->update($user_account, [
                'mobile_number' => $attr['contact_no'],
                'password' => bcrypt($attr['rsbsa_number']),
                'pin_code' => bcrypt(substr($attr['rsbsa_number'], -4)),
                'verified' => 1,
            ]);

            $addOrUpdate = $this->userProfileService->update($user_account, $attr);
            $audit_remarks = $authUser . " Profile Information has been successfully updated.";
            $this->logHistoryService->logUserHistory($authUser, "", SquidPayModuleTypes::updateProfile, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);
            DB::commit();
            return $addOrUpdate;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function batchUpload($file, string $authUser)
    {
        ini_set('max_execution_time', 300);

        $import = new FarmersImport($this->userAccountRepository,
            $this->userDetailRepository,
            $this->userAccountNumbers,
            $this->maritalStatus,
            $this->userBalanceInfo,
            $authUser);

        Excel::import($import, $file);

        $failFilename = 'farmers/' . date('Y-m-d') . '-farmerFailedUploadList.csv';
        $successFilename = 'farmers/' . date('Y-m-d') . '-farmerSuccessUploadList.csv';

        Excel::store(new FailedUploadExport($import->getFails()), $failFilename, 's3');
        Excel::store(new SuccessUploadExport($import->getSuccesses()), $successFilename, 's3');

        return [
            'fail_file' => Storage::disk('s3')->temporaryUrl($failFilename, Carbon::now()->addMinutes(30)),
            'success_file' => Storage::disk('s3')->temporaryUrl($successFilename, Carbon::now()->addMinutes(30))
        ];
    }

    public function subsidyBatchUpload($file, string $authUser) {
        $filename = $file->getClientOriginalName();
        $import = new SubsidyImport($this->userAccountRepository, $this->inReceiveFromDBP, $this->userTransactionHistoryRepository, $this->userBalanceInfo, $this->referenceNumberService, $authUser, $filename );
        Excel::import($import, $file);

        $failFilename = 'farmers/' . date('Y-m-d') . '-farmerSubsidyFailedUploadList.xlsx';
        $successFilename = 'farmers/' . date('Y-m-d') . '-farmerSubsidySuccessUploadList.xlsx';

        Excel::store(new SubsidyFailedUploadExport($import->getFails()), $failFilename, 's3');
        Excel::store(new SubsidySuccessUploadExport($import->getSuccesses()), $successFilename, 's3');

        return [
            'fail_file' => Storage::disk('s3')->temporaryUrl($failFilename, Carbon::now()->addMinutes(30)),
            'success_file' => Storage::disk('s3')->temporaryUrl($successFilename, Carbon::now()->addMinutes(30))
        ];
    }

    public function processBatchUpload(UploadedFile $file, string $userId)
    {
        $user = $this->userAccountRepository->getUser($userId);
        $result = $this->batchUpload($file, $userId);
        $this->emailService->batchUploadNotification($user, $result['success_file'], $result['fail_file']);
    }

    // UPLOADING V2
    public function batchUploadV2($file, string $authUser)
    {
        ini_set('max_execution_time', 300);

        // UPLOAD FIRST
        $fileExt = $this->getFileExtensionName($file);
        $fileName = request()->user()->id . "/" . \Str::random(40) . "." . $fileExt;
        $filePath = $this->saveFile($file, $fileName, 'dbp_uploads');

        $import = new FarmerAccountImport($this->userDetail, request()->user()->id, $this->maritalStatus, $this->userAccountNumbers, $this->userAccountRepository, $this->userBalanceInfo);
        Excel::import($import, $filePath, 's3');

        $errors = $import->getFails();
        $success = $import->getSuccesses();
        $headers = $import->getHeaders();

        $failFilename = 'farmers/' . date('Y-m-d') . '-farmerFailedUploadList.csv';
        $successFilename = 'farmers/' . date('Y-m-d') . '-farmerSuccessUploadList.csv';

        Excel::store(new FailedExport($errors, $headers->toArray()), $failFilename, 's3');
        Excel::store(new SuccessExport($success, $headers->toArray()), $successFilename, 's3');

        return [
            'fail_file' => Storage::disk('s3')->temporaryUrl($failFilename, Carbon::now()->addMinutes(30)),
            'success_file' => Storage::disk('s3')->temporaryUrl($successFilename, Carbon::now()->addMinutes(30))
        ];
    }
}
