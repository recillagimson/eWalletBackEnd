<?php

namespace App\Services\Utilities\Verification;

use App\Enums\eKYC;
use App\Enums\SquidPayModuleTypes;
use App\Repositories\IdType\IIdTypeRepository;
use App\Repositories\Tier\ITierApprovalCommentRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\KYCService\IKYCService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VerificationService implements IVerificationService
{
    public IUserPhotoRepository $userPhotoRepository;
    public IUserDetailRepository $userDetailRepository;
    public IUserSelfiePhotoRepository $userSelfiePhotoRepository;
    public ILogHistoryService $logHistoryService;
    public IIdTypeRepository $iIdTypeRepository;
    public ITierApprovalCommentRepository $tierApprovalComment;
    private IKYCService $kycService;

    public function __construct(IUserPhotoRepository $userPhotoRepository,
                                IUserDetailRepository $userDetailRepository,
                                IUserSelfiePhotoRepository $userSelfiePhotoRepository,
                                ILogHistoryService $logHistoryService,
                                IIdTypeRepository $iIdTypeRepository,
                                ITierApprovalCommentRepository $iTierApprovalCommentRepository,
                                IKYCService $kycService)
    {
        $this->userPhotoRepository = $userPhotoRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->userSelfiePhotoRepository = $userSelfiePhotoRepository;
        $this->logHistoryService = $logHistoryService;
        $this->iIdTypeRepository = $iIdTypeRepository;
        $this->iTierApprovalCommentRepository = $iTierApprovalCommentRepository;
        $this->kycService = $kycService;
    }

    public function createSelfieVerification(array $data, ?string $userAccountId = null)
    {
        // Delete existing first
        // Get details first
        $userDetails = $this->userDetailRepository->getByUserId($userAccountId ? $userAccountId : request()->user()->id);
        // If no user Details
        if(!$userDetails) {
            throw ValidationException::withMessages([
                'user_detail_not_found' => 'User Detail not found'
            ]);
        }
        // Delete file using path from current detail
        // $this->deleteFile($userDetails->selfie_loction);

        // For Serfile Processing
        // GET EXT NAME
        $selfiePhotoExt = $this->getFileExtensionName($data['selfie_photo']);
        // GENERATE NEW FILE NAME
        $selfiePhotoName = request()->user()->id . "/" . \Str::random(40) . "." . $selfiePhotoExt;
        // PUT FILE TO STORAGE
        $selfiePhotoPath = $this->saveFile($data['selfie_photo'], $selfiePhotoName, 'selfie_photo');

        $data['photo_location'] = $selfiePhotoPath;
        $data['user_account_id'] = $userAccountId ? $userAccountId : request()->user()->id;
        $data['user_created'] = request()->user()->id;
        $data['user_updated'] = request()->user()->id;
        // SAVE SELFIE LOCATION ON USER DETAILS
        $record = $this->userSelfiePhotoRepository->create($data);

        if(isset($data['remarks'])) {
            $this->iTierApprovalCommentRepository->create([
                'tier_approval_id' => isset($data['tier_approval_id']) ? $data['tier_approval_id'] : "",
                'remarks' => isset($data['remarks']) ? $data['remarks'] : "",
                'user_created' => $data['user_created'] = request()->user()->id,
                'user_updated' => $data['user_updated'] = request()->user()->id
            ]);
        }

        $audit_remarks = request()->user()->account_number . "  has uploaded Selfie";
        $this->logHistoryService->logUserHistory(request()->user()->id, "", SquidPayModuleTypes::uploadSelfiePhoto, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);


        return $record;
        // return to controller all created records
    }

    public function create(array $data) {

        $idType = $this->iIdTypeRepository->get($data['id_type_id']);
        if(!$idType) {
            throw ValidationException::withMessages([
                'id_type_not_found' => 'Id Type not found'
            ]);
        }

        $recordsCreated = [];
        // PROCESS IDS
        foreach($data['id_photos'] as $idPhoto) {
            // Get file extension name
            $extName = $this->getFileExtensionName($idPhoto);
            // Generate new file name
            $idPhotoName = $data['user_account_id'] . "/" . \Str::random(40) . "." . $extName;
            // Put file to storage
            $path = $this->saveFile($idPhoto, $idPhotoName, 'id_photo');
            // Save record to DB
            $params = [
                'user_account_id' => $data['user_account_id'],
                'id_type_id' => $data['id_type_id'],
                'photo_location' => $path,
                'user_created' => request()->user()->id,
                'user_updated' => request()->user()->id,
                'id_number' => $data['id_number'],
                'tier_approval_id' => isset($data['tier_approval_id']) ? $data['tier_approval_id'] : "",
                'remarks' => isset($data['remarks']) ? $data['remarks'] : ""
            ];
            $record = $this->userPhotoRepository->create($params);

            $eKYC = $this->getHVResponse($idPhoto, $data['id_type_id']);
            $extractData = $this->extractData($eKYC);

            if(isset($data['remarks'])) {
                $this->iTierApprovalCommentRepository->create([
                    'tier_approval_id' => isset($data['tier_approval_id']) ? $data['tier_approval_id'] : "",
                    'remarks' => isset($data['remarks']) ? $data['remarks'] : "",
                    'user_created' => $data['user_created'] = request()->user()->id,
                    'user_updated' => $data['user_updated'] = request()->user()->id
                ]);
            }

            // Collect created record
            $record->ekyc = $extractData;
            array_push($recordsCreated, $record);

            $audit_remarks = request()->user()->account_number . "  has uploaded " . $idType->type . ", " . $idType->description;
            $this->logHistoryService->logUserHistory(request()->user()->id, "", SquidPayModuleTypes::uploadIdPhoto, "", Carbon::now()->format('Y-m-d H:i:s'), $audit_remarks);
        }



        return $recordsCreated;
    }

    private function extractData($response) {
        $data = [];
        if($response && isset($response['result']) && isset($response['result']['0']) && $response['result']['0']->details) {
            $response_data = $response['result']['0']->details;

            foreach($response_data as $key => $entry) {

                // CHECK IF key value is necessary field
                if(in_array($key, eKYC::returnableFields)) {
                    if($entry && $entry->value) {
                        $data[$key] = $entry->value;
                    }
                }
            }
            return $data;
        }
        return $response;
    }

    private function getHVResponse($idPhoto, $idTypeId) {
        // CHECK ID TYPE FOR EKYC
        $idType = $this->iIdTypeRepository->get($idTypeId);
        // Check if DL
        if($idType && $idType->is_ekyc == 1) {
            switch($idTypeId) {
                case eKYC::DL: {
                    return $this->kycService->initOCR(['id_photo' => $idPhoto], 'phl_dl');
                    break;
                }
                case eKYC::PRC: {
                    return $this->kycService->initOCR(['id_photo' => $idPhoto], 'prc');
                    break;
                }
                case eKYC::Passport: {
                    return $this->kycService->initOCR(['id_photo' => $idPhoto], 'passport');
                    break;
                }
                default: {
                    return $this->kycService->initOCR(['id_photo' => $idPhoto]);
                    break;
                }
            }
        }
        return [
            'message' => 'for manual KYC'
        ];
    }

    // Get file extension name
    public function getFileExtensionName(UploadedFile $file) {
        return $file->getClientOriginalExtension();
    }

    // Move uploaded file to server storage
    // Returns path of the file stored
    public function saveFile(UploadedFile $file, $fileName, $folderName) {
        return Storage::disk('s3')->putFileAs($folderName, $file, $fileName);
    }

    // Delete existing file if necessary
    public function deleteFile($path) {
        return Storage::disk('s3')->delete($path);
    }

    // Get Signed URL
    public function getSignedUrl(string $userPhotoId) {
        $userPhoto = $this->userPhotoRepository->get($userPhotoId);
        if(!$userPhoto) {
            throw ValidationException::withMessages([
                'user_photo_not_found' => "User Photo not found"
            ]);
        }

        if($userPhoto && $userPhoto->photo_location) {
            return Storage::disk('s3')->temporaryUrl($userPhoto->photo_location, Carbon::now()->addMinutes(5));
        }

        throw ValidationException::withMessages([
            'photo_url_empty' => 'User Photo location not found'
        ]);
    }

    // UPDATE TIER APPROVAL IDS OF USER PHOTOS AND SELFIE PHOTOS
    public function updateTierApprovalIds(array $userIdPhotos, array $userSelfiePhotos, string $tierApprovalStatus, bool $is_farmer=false) {
        // USER ID PHOTOS
        foreach($userIdPhotos as $photo) {
            $photo_instance = $this->userPhotoRepository->get($photo);

            $this->userPhotoRepository->update($photo_instance, [
                'tier_approval_id' => $tierApprovalStatus,
                'status' => $is_farmer ? 'APPROVED' : "PENDING",
                'approved_by' => $is_farmer ? eKYC::eKYC : '',
                'remarks' => $is_farmer ? eKYC::eKYC_remarks : ''
            ]);
        }
        // USER SELFIE PHOTOS
        foreach($userSelfiePhotos as $photo) {
            $photo_instance = $this->userSelfiePhotoRepository->get($photo);
            $this->userSelfiePhotoRepository->update($photo_instance, [
                'tier_approval_id' => $tierApprovalStatus,
                'status' => $is_farmer ? 'APPROVED' : "PENDING",
                'approved_by' => $is_farmer ? eKYC::eKYC : '',
                'remarks' => $is_farmer ? eKYC::eKYC_remarks : ''
            ]);
        }
    }
}
