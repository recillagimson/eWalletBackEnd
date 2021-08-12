<?php

namespace App\Services\Utilities\Verification;

use App\Enums\eKYC;
use App\Enums\SquidPayModuleTypes;
use App\Repositories\IdType\IIdTypeRepository;
use App\Repositories\KYCVerification\IKYCVerificationRepository;
use App\Repositories\Tier\ITierApprovalCommentRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\KYCService\IKYCService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Log;
use Str;

class VerificationService implements IVerificationService
{
    public IUserPhotoRepository $userPhotoRepository;
    public IUserDetailRepository $userDetailRepository;
    public IUserSelfiePhotoRepository $userSelfiePhotoRepository;
    public ILogHistoryService $logHistoryService;
    public IIdTypeRepository $iIdTypeRepository;
    public ITierApprovalCommentRepository $tierApprovalComment;
    private IKYCService $kycService;
    private IUserAccountRepository $userAccountService;
    private IKYCVerificationRepository $kycRepository;

    public function __construct(IUserPhotoRepository $userPhotoRepository,
                                IUserDetailRepository $userDetailRepository,
                                IUserSelfiePhotoRepository $userSelfiePhotoRepository,
                                ILogHistoryService $logHistoryService,
                                IIdTypeRepository $iIdTypeRepository,
                                ITierApprovalCommentRepository $iTierApprovalCommentRepository,
                                IKYCService $kycService,
                                IUserAccountRepository $userAccountService,
                                IKYCVerificationRepository $kycRepository)
    {
        $this->userPhotoRepository = $userPhotoRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->userSelfiePhotoRepository = $userSelfiePhotoRepository;
        $this->logHistoryService = $logHistoryService;
        $this->iIdTypeRepository = $iIdTypeRepository;
        $this->iTierApprovalCommentRepository = $iTierApprovalCommentRepository;
        $this->kycService = $kycService;
        $this->userAccountService = $userAccountService;
        $this->kycRepository = $kycRepository;
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
        $selfiePhotoName = request()->user()->id . "/" . Str::random(40) . "." . $selfiePhotoExt;
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
            $idPhotoName = $data['user_account_id'] . "/" . Str::random(40) . "." . $extName;
            // Put file to storage
            $path = $this->saveFile($idPhoto, $idPhotoName, 'id_photo');
            // Save record to DB

            // Init eKYC OCR
            $eKYC = $this->getHVResponse($idPhoto, $data['id_type_id']);
            $extractData = $this->extractData($eKYC, $idType->type);

            $params = [
                'user_account_id' => $data['user_account_id'],
                'id_type_id' => $data['id_type_id'],
                'photo_location' => $path,
                'user_created' => request()->user()->id,
                'user_updated' => request()->user()->id,
                'id_number' => isset($extractData['id_number']) && $extractData['id_number'] != 'N/A' ? $extractData['id_number'] : $data['id_number'],
                'tier_approval_id' => isset($data['tier_approval_id']) ? $data['tier_approval_id'] : "",
                'remarks' => isset($data['remarks']) ? $data['remarks'] : ""
            ];
            $record = $this->userPhotoRepository->create($params);


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

    private function extractData($response, $idType) {
        $data = $response;
        Log::info(json_encode($response));
        if($response && isset($response['result']) && isset($response['result']['0']) && $response['result']['0']->details) {
            $response_data = $response['result']['0']->details;

            $templateResponse = [
                'full_name' => 'N/A',
                'first_name' => 'N/A',
                'last_name' => 'N/A',
                'middle_name' => 'N/A',
                'id_type' => $idType,
                'id_number' => 'N/A',
                'expiration_date' => 'N/A',
            ];

            foreach($response_data as $key => $entry) {


                // CHECK IF key value is necessary field
                if(in_array($key, eKYC::returnableFields)) {

                    // CHECK IF LAST NAME
                    if(in_array($key, eKYC::lastNameKey)) {
                        $templateResponse['last_name'] = $entry->value;
                    }

                    // CHECK IF FIRST NAME
                    if(in_array($key, eKYC::firstNameKey)) {
                        $templateResponse['first_name'] = $entry->value;
                    }

                    // CHECK IF MIDDLE NAME
                    if(in_array($key, eKYC::middleNameKey)) {
                        $templateResponse['middle_name'] = $entry->value;
                    }

                    // CHECK IF FULL NAME
                    if(in_array($key, eKYC::fullNameKey)) {
                        $templateResponse['full_name'] = $entry->value;
                    }

                    // CHECK IF ID Number
                    if(in_array($key, eKYC::idNumberKey)) {
                        $templateResponse['id_number'] = $entry->value;
                    }

                    // CHECK IF DOE
                    if(in_array($key, eKYC::expirationDateKey)) {
                        $templateResponse['expiration_date'] = $entry->value;
                    }

                    // if($entry && $entry->value) {
                    //     $data[$key] = $entry->value;
                    // }
                }
            }

            // CHECK CONFLICT FOR FULL NAME and FIRST NAME
            if($templateResponse['last_name'] != 'N/A') {
                $templateResponse['full_name'] = 'N/A';
            } else {
                $templateResponse['first_name'] = 'N/A';
            }
            return $templateResponse;
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

    public function createSelfieVerificationFarmers(array $data, ?string $userAccountId = null)
    {
        // Delete existing first
        // Get details first
        $userDetails = $this->userDetailRepository->getByUserId($userAccountId ? $userAccountId : request()->user()->id);
        $userAccount = $this->userAccountService->get($userAccountId);

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
        $selfiePhotoName = request()->user()->id . "/" . Str::random(40) . "." . $selfiePhotoExt;
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


        $res = $this->kycService->verify([
            'dob' => $userDetails['birth_date'],
            'name' => $userDetails['first_name'] . " " . $userDetails['last_name'],
            'id_number' => "111",
            'user_account_id' => $userAccountId,
            'selfie' => $data['selfie_photo'],
            'nid_front' => $data['id_photo'],
        ], false);

        return [
            'selfie_record' => $record,
            'dedupe' => $res
        ];
        // return to controller all created records
    }
}
