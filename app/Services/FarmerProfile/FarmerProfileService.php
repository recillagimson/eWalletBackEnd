<?php

namespace App\Services\FarmerProfile;

use DB;
use Str;
use Exception;
use App\Enums\eKYC;
use Illuminate\Http\File;
use App\Enums\AccountTiers;
use App\Enums\SuccessMessages;
use App\Traits\HasFileUploads;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use App\Enums\SquidPayModuleTypes;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Support\Facades\Storage;
use App\Services\KYCService\IKYCService;
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
                    'user_created' => $authUser,
                    'user_updated' => $authUser,
                    'transaction_number' => $generatedTransactionNumber,
                ]);
                $this->verificationService->updateTierApprovalIds($attr['id_photos_ids'], $attr['id_selfie_ids'], $tierApproval->id, true);
                $audit_remarks = $user_account->id . " has requested to upgrade to Silver";
                $record = $this->logHistoryService->logUserHistory($user_account->id, "", SquidPayModuleTypes::upgradeToSilver, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);
                // $this->userAccountRepository->update($user_account, ['tier_id' => AccountTiers::tier2]);
            // } //COMMENT FOR TESTING PURPOSES
            // $details = $request->validated();
            // dd($user_account->profile);

            $this->userAccountRepository->update($user_account, [
                'password' => bcrypt($attr['rsbsa_number']),
                'pin_code' => bcrypt(substr($attr['rsbsa_number'], -4)),
            ]);

            $addOrUpdate = $this->userProfileService->update($user_account, $attr);
            $audit_remarks = $authUser . " Profile Information has been successfully updated.";
            $this->logHistoryService->logUserHistory($authUser, "", SquidPayModuleTypes::updateProfile, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);
            \DB::commit();
            return $addOrUpdate;
        } catch(\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function batchUpload($file, string $authUser)
    {
        ini_set('max_execution_time', 30000);

        $import = new FarmersImport($this->userAccountRepository,
            $this->userDetailRepository,
            $this->userAccountNumbers,
            $this->maritalStatus,
            $this->userBalanceInfo,
            $authUser);

        $filename = date('Y-m-d') . '-' . $file->getClientOriginalName();

        Storage::disk('s3')->putFileAs('farmers', $file, $filename);

        Excel::import($import, 'farmers/' . $filename, 's3');

        $date = date('ymd');
        $prov = $import->getProv();
        $seq = FarmerImport::where(function($q) use ($prov){
            $q->where('province', $prov);
            $q->whereDate('created_at', Carbon::today());
        })->count();

        $imp = FarmerImport::create([
            "filename" => $filename,
            'prov' => $prov,
            'seq' => ++$seq
        ]);

        $seq = str_pad($seq, 3, "0", STR_PAD_LEFT);

        $failFilename = "farmers/ONBSUCRFFA{$prov}SPTI{$date}{$seq}.csv";
        $successFilename = "farmers/ONBEXPRFFA{$prov}SPTI{$date}{$seq}.csv";

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

    public function uploadFileToS3($file, string $folder = null) {
        $fileExt = $this->getFileExtensionName($file);
        $fileName = request()->user()->id . "/" . Str::random(40) . "." . $fileExt;
        return $this->saveFile($file, $fileName, $folder ? $folder : 'dbp_uploads');
    }

    // UPLOADING V2
    public function batchUploadV2(string $filePath, string $authUser)
    {
        ini_set('max_execution_time', 30000);
        ini_set('memory_limit', '-1');

        $import = new FarmerAccountImportV2($this->userDetail, $authUser, $this->maritalStatus, $this->userAccountNumbers, $this->userAccountRepository, $this->userBalanceInfo);
        // $import = new FarmerAccountImportV2($this->userDetail, request()->user()->id, $this->maritalStatus, $this->userAccountNumbers, $this->userAccountRepository, $this->userBalanceInfo);
        Excel::import($import, $filePath, 's3');
        $errors = $import->getFails();
        $success = $import->getSuccesses();
        $headers = $import->getHeaders();
        $prov = $import->getProv();

        $seq = $this->farmerImportRepository->countSequnceByProvinceAndDateCreated($prov, Carbon::now()->format('Y-m-d'));


        $imp = $this->farmerImportRepository->create([
            'filename' => $filePath,
            'province' => $prov,
            'seq' => $seq > 0 ? ($seq + 1) : 1,
        ]);

        $seq = ($seq + 1);
        $formatSeq = $seq;

        if($seq < 10) {
            $formatSeq = "00" . $seq;
        } else if($seq < 100) {
            $formatSeq = "0" . $seq;
        }

        $province = $this->provinceRepository->getProvinceByName($prov);

        $date = date('ymd');
        $successFilename = "farmers/ONBSUCRFFA{$province->da_province_code}SPTI{$date}{$formatSeq}.xlsx";
        $failFilename = "farmers/ONBEXPRFFA{$province->da_province_code}SPTI{$date}{$formatSeq}.xlsx";

        Excel::store(new FailedExport($errors, $headers->toArray()), $failFilename, 's3');
        Excel::store(new SuccessExport($success, $headers->toArray()), $successFilename, 's3');

        // // ADD NOTIFICATION TO AUTH USER
        $this->notificationRepository->create([
            'user_account_id' => $authUser,
            'title' => 'DBP Upload',
            'description' => Storage::disk('s3')->temporaryUrl($failFilename, Carbon::now()->addMinutes(30)) . ', '
                . Storage::disk('s3')->temporaryUrl($successFilename, Carbon::now()->addMinutes(30)),
            'status' => 1,
            'user_created' => $authUser,
            'user_updated' => $authUser
        ]);

        return new Collection([
            'fail_file' => Storage::disk('s3')->temporaryUrl($failFilename, Carbon::now()->addMinutes(30)),
            'success_file' => Storage::disk('s3')->temporaryUrl($successFilename, Carbon::now()->addMinutes(30)),
        ]);
    }

        // UPLOADING V2
    public function subsidyProcess(string $filePath, string $authUser) {
        ini_set('max_execution_time', 30000);
        ini_set('memory_limit', '-1');

        $import = new FarmerSubsidyImportV2($authUser, $this->userAccountRepository, $this->transactionCategoryRepository, $this->dbpRepository, $this->referenceNumberService, $filePath, $this->userBalanceInfo, $this->userTransactionHistoryRepository);
        Excel::import($import, $filePath, 's3');

        $errors = $import->getFails();
        $success = $import->getSuccesses();

        $successFilename = "farmers/subsidy_success-" . Carbon::now()->format('YmdHisA') ."-" .".csv";
        $failFilename = "farmers/subsidy_fail" . Carbon::now()->format('YmdHisA') ."-" . ".csv";

        Excel::store(new SubsidyFailedExport($errors), $failFilename, 's3');
        Excel::store(new SubsidySuccessExport($success), $successFilename, 's3');


        // ADD NOTIFICATION TO AUTH USER
        $this->notificationRepository->create([
            'user_account_id' => $authUser,
            'title' => 'DBP Upload',
            'description' => Storage::disk('s3')->temporaryUrl($failFilename, Carbon::now()->addMinutes(30)) . ', '
                . Storage::disk('s3')->temporaryUrl($successFilename, Carbon::now()->addMinutes(30)),
            'status' => 1,
            'user_created' => $authUser,
            'user_updated' => $authUser
        ]);

        return new Collection([
            'fail_file' => Storage::disk('s3')->temporaryUrl($failFilename, Carbon::now()->addMinutes(30)),
            'success_file' => Storage::disk('s3')->temporaryUrl($successFilename, Carbon::now()->addMinutes(30)),
        ]);
    }

    public function DBPTransactionReport(array $attr, string $authUser) {
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';

        $records = [];
        if($attr && isset($attr['type']) && $attr['type'] == 'API') {
            $records = $this->userTransactionHistoryRepository->getDBPTransactionHistory($attr, $authUser);
        } else {
            $records = $this->userTransactionHistoryRepository->getDBPTransactionHistory($attr, $authUser);
        }
        if($attr && isset($attr['from']) && isset($attr['to'])) {
            $from = $attr['from'];
            $to = $attr['to'];
        }
        if($attr && isset($attr['type'])) {
            $type = $attr['type'];
        }

        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        if($type === 'CSV') {
            Excel::store(new DBPTransactionExport($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);

            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } else if($type === 'XLSX') {
            Excel::store(new DBPTransactionExport($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);

        } else {
            // return $records->toArray();
            return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
        }
    }

    public function s3TempUrl(string $generated_link) {
        $temp_url = Storage::disk('s3')->temporaryUrl($generated_link, Carbon::now()->addMinutes(30));
        return $temp_url;
    }

}
