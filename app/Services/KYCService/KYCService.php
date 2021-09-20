<?php

namespace App\Services\KYCService;

use App\Enums\SuccessMessages;
use App\Repositories\KYCVerification\IKYCVerificationRepository;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;
use App\Services\Utilities\CurlService\ICurlService;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\Errors\WithKYCErrors;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Errors\WithUserErrors;
use Carbon\Carbon;
use CURLFILE;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;

class KYCService implements IKYCService
{
    use WithKYCErrors, WithTransactionErrors, WithUserErrors;

    private ICurlService $curlService;
    private IUserSelfiePhotoRepository $userSelfiePhotoRepository;
    private IUserAccountRepository $userAccountRepository;
    private IResponseService $responseService;
    private IKYCVerificationRepository $kycRepository;
    private ITierApprovalRepository $tierApproval;

    private $appId;
    private $appKey;
    private $faceMatchUrl;
    private $ocrUrl;
    private $ocrPassportUrl;
    private $verifyUrl;
    private $callBackUrl;
    private $enrolId;

    public function __construct(ICurlService $curlService, IUserSelfiePhotoRepository $userSelfiePhotoRepository, IUserAccountRepository $userAccountRepository, IResponseService $responseService, IKYCVerificationRepository $kycRepository, ITierApprovalRepository $tierApproval)
    {
        $this->curlService = $curlService;
        $this->userSelfiePhotoRepository = $userSelfiePhotoRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->responseService = $responseService;
        $this->kycRepository = $kycRepository;
        $this->tierApproval = $tierApproval;

        $this->appId = config('ekyc.appId');
        $this->appKey = config('ekyc.appKey');
        $this->faceMatchUrl = config('ekyc.faceMatchUrl');
        $this->ocrUrl = config('ekyc.ocrUrl');
        $this->ocrPassportUrl = config('ekyc.ocrPassportUrl');
        $this->verifyUrl = config('ekyc.verifyUrl');
        $this->callBackUrl = config('ekyc.callbackUrl');
        $this->enrolId = config('ekyc.enrolId');

    }

    private function getAuthorizationHeaders(): array
    {
        $headers = array(
            'appId: ' . $this->appId,
            'appKey: '. $this->appKey,
            'accept: '. 'application/json',
            'content-type: ' . 'multipart/form-data'
        );
        return $headers;
    }

    public function initFaceMatch(array $attr, bool $isPath = false) {
        $url = $this->faceMatchUrl;
        $headers = $this->getAuthorizationHeaders();

        if($isPath) {
            $id = new CURLFILE($attr['id_photo']);
            $selfie = new CURLFILE($attr['selfie_photo']);
        } else {
            $id = new CURLFILE($attr['id_photo']->getPathname());
            $selfie = new CURLFILE($attr['selfie_photo']->getPathname());
        }

        $data = array('id' => $id, 'selfie' => $selfie);

        return $this->curlService->curlPost($url, $data, $headers);
    }

    public function initOCR(array $attr, $idType = '')
    {
        $url = $this->ocrUrl;
        if ($idType == 'passport') {
            $url = $this->ocrPassportUrl;
        }
        $headers = $this->getAuthorizationHeaders();
        $headers[4] = "transactionId: " . (string)Str::uuid();
        $id = new CURLFILE($attr['id_photo']->getPathname());

        $data = array('id' => $id);
        if ($idType) {
            // $headers[5] = "cardType: " . $idType;
            $data['cardType'] = $idType;
        }

        return $this->curlService->curlPost($url, $data, $headers);
    }

    public function initMerchantFaceMatch(array $attr) {

        $user = $this->userAccountRepository->getUserByAccountNumber($attr['account_number']);
        $selfie = $this->userSelfiePhotoRepository->getSelfieByAccountNumber($user->id);

        $selfie_s3 = Storage::disk('s3')->temporaryUrl($selfie->photo_location, Carbon::now()->addMinutes(10));
        // dd($selfie_s3);
        $filename = Str::random(20);
        $tempImage = tempnam(sys_get_temp_dir(), $filename);
        copy($selfie_s3, $tempImage);

        $url = $this->faceMatchUrl;
        $headers = $this->getAuthorizationHeaders();

        $selfie_retrieved = new CURLFILE($tempImage);
        $selfie = new CURLFILE($attr['selfie_photo']->getPathname());

        $data = array('id' => $selfie_retrieved, 'selfie' => $selfie);

        $reponse = $this->curlService->curlPost($url, $data, $headers);
        unlink($tempImage);
        return $reponse;
    }

    public function checkIDExpiration(array $attr, $idType = 'phl_dl'): array
    {

        $initOCR = $this->initOCR([
            'id_photo' => $attr['id_photo']
        ], $idType);

        if ($initOCR && isset($initOCR['result']) && isset($initOCR['result']['0']) && $initOCR['result']['0']->details && $initOCR['result']['0']->details->doe) {
            // GET DATE OF EXPIRATION
            $doe = $initOCR['result']['0']->details->doe;
            $expDate = Carbon::parse($doe->value);
            $now = Carbon::now();

            // NOT Expired
            if($expDate->greaterThan($now)) {
                return [
                    'message' => 'ID not expired',
                    'data' => $doe
                ];
            } else {
                return [
                    'message' => 'ID expired',
                    'data' => $doe
                ];
            }
        }

        return $initOCR;

    }

    public function matchOCR(array $attr) {
        if(isset($attr['manual_input']) && isset($attr['ocr_response'])) {
            if(isset($attr['manual_input']['full_name']) && isset($attr['ocr_response']['full_name'])) {
                if(strtolower($attr['ocr_response']['full_name']) == strtolower($attr['manual_input']['full_name'])) {
                    // return [
                    //     'message' => 'OCR and Input data match'
                    // ];
                    return $this->responseService->successResponse([
                        'message' => 'OCR and Input data match'
                    ], SuccessMessages::success);
                }
            }

            if(isset($attr['manual_input']['first_name']) && isset($attr['ocr_response']['first_name']) && isset($attr['manual_input']['last_name']) && isset($attr['ocr_response']['last_name'])) {
                if(strtolower($attr['ocr_response']['first_name']) == strtolower($attr['manual_input']['first_name']) && strtolower($attr['ocr_response']['last_name']) == strtolower($attr['manual_input']['last_name'])) {
                    // return [
                    //     'message' => 'OCR and Input data match'
                    // ];
                    return $this->responseService->successResponse([
                        'message' => 'OCR and Input data match'
                    ], SuccessMessages::success);
                }
            }
        }

        return $this->OCRmatchOCR();
        // return [
        //     'message' => 'OCR and Input data not match'
        // ];
    }


    public function isEKYCValidated(array $params): bool
    {
        if ($params && isset($params['ocr_response'])) {
            // CHECK IF FULLNAME
            if ($params['ocr_response'] && isset($params['ocr_response']['last_name']) && $params['ocr_response']['last_name'] == '') {
                // Build full name
                $last_name = $params['last_name'];
                $first_name = $params['first_name'];
                $middle_name = isset($params['middle_name']) ? $params['middle_name'] : '';
                // Last Name, First Name Middle Name
                $full_name_LFM = strtolower($last_name . ", " . $first_name . " " . $middle_name);
                // First Name Middle Name Last Name
                $full_name_FML = strtolower($first_name . ", " . $middle_name . " " . $last_name);
                // LAST NAME FIRST NAME MI
                $full_name_FMNi = strtolower($last_name . " " . $first_name . ", " . substr($middle_name, 0, 1));

                // FULL NAME MATCH TO KNOW PATTERNS
                if($full_name_FML == strtolower($params['ocr_response']['full_name']) || $full_name_LFM == strtolower($params['ocr_response']['full_name']) || $full_name_FMNi == strtolower($params['ocr_response']['full_name'])) {
                    return true;
                }
            }
            // ELSE BREAKDOWNED VERSION
            else if($params['ocr_response'] && isset($params['last_name']) && isset($params['first_name'])) {
                $ocr_respose = $params['ocr_response'];
                $last_name = strtolower($params['last_name']);
                $first_name = strtolower($params['first_name']);
                $middle_name = isset($params['middle_name']) ? strtolower($params['middle_name']) : '';
                if(strtolower($ocr_respose['first_name']) == $first_name && strtolower($ocr_respose['middle_name']) == $middle_name && strtolower($ocr_respose['last_name']) == $last_name) {
                    return true;
                }
            }
        }


        // CANT READ OCR RESPONSE
        return false;
    }

    public function verify(array $attr, $from_api = true) {
        DB::beginTransaction();

        try {
            $url = $this->verifyUrl;
            $headers = $this->getAuthorizationHeaders();

            $nid = $attr['nid_front'];
            $selfie = $attr['selfie'];

            if(!is_string($attr['nid_front'])) {
                $nid = $attr['nid_front']->getPathname();
                $selfie = $attr['selfie']->getPathname();
            }


            $selfieFile = new CURLFILE($selfie);
            // $selfieFile = file_get_contents($nid);
            $frontFile = new CURLFILE($nid);
            // $frontFile = file_get_contents($selfie);

            $data = [
                'callbackURL' => $this->callBackUrl,
                'name' => $attr['name'],
                'idNumber' => $attr['id_number'],
                'dob' => Carbon::parse($attr['dob'])->format('d-m-Y'),
                'applicationId' => Str::uuid(),
                'enrol' => $this->enrolId,
                'selfie' => $selfieFile,
                'idFront' => $frontFile,
            ];

            $response = $this->curlService->curlPost($url, $data, $headers);

            $error = '';
            if($response && isset($response['status'])) {
                $error = $response['status'];
            }

            $record = $this->kycRepository->create([
                'user_account_id' => $attr['user_account_id'],
                'request_id' => isset($response['result']) ? $response['result']->requestId : $response['requestId'],
                'application_id' => isset($response['result']) ? $response['result']->applicationId : $response['applicationId'],
                'hv_response' => json_encode($response),
                'hv_result' => $error,
            ]);

            if ($response && isset($response['statusCode']) && $response['statusCode'] == 200 && isset($response['result']) && $response['result']) {
                DB::commit();
                // WAIT FOR CALLBACK
                sleep(5);
                // $record = $this->kycRepository->findByRequestId($record->request_id);
                if($from_api) {
                    return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
                }
                return $record;
            } else {
                // \DB::rollBack();
                DB::commit();
                // ERROR
                if($from_api) {
                    // return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
                    return $this->responseService->successResponse([
                        'statusCode' => $response['statusCode'],
                        'message' => $response['error'],
                        'status' => $response['status']
                    ], SuccessMessages::success);
                }
                return $record;
            }

        } catch (Exception $err) {
            Log::info(json_encode($err->getMessage()));
            DB::rollBack();
            $this->kycVerifyFailed($err->getMessage());
        }
    }

    public function handleCallback(array $attr): JsonResponse
    {
        Log::info(json_encode($attr));
        if ($attr && isset($attr['result']) && isset($attr['result']['requestId'])) {
            $record = $this->kycRepository->findByRequestId($attr['result']['requestId']);
            $tierApproval = $this->tierApproval->getLatestRequestByUserAccountId($record->user_account_id);
            if($tierApproval && $record->hv_result != 'green') {
                $tierApproval->update([
                    'status' => 'DECLINED'
                ]);
            }
            if ($record) {
                $this->kycRepository->update($record, [
                    'hv_response' => json_encode($attr),
                    'hv_result' => isset($attr['result']['unifiedResult']['channel']) ? $attr['result']['unifiedResult']['channel'] : $attr['error'],
                    'status' => 'CALLBACK_RECEIVED'
                ]);
            }
        }

        if($attr && isset($attr['error'])) {
            $record = $this->kycRepository->findByRequestId($attr['requestId']);
            if($record) {
                $this->kycRepository->update($record, [
                    'hv_response' => json_encode($attr),
                    'hv_result' => $attr['error'],
                    'status' => 'CALLBACK_RECEIVED_ERROR'
                ]);
            }
        }

        return response()->json([
            'message' => 'Callback Received'
        ], 200);
    }

    public function verifyRequest(string $requestId): JsonResponse
    {
        $record = $this->kycRepository->findByRequestId($requestId);
        if($record) {
            $tierApproval = $this->tierApproval->getLatestRequestByUserAccountId($record->user_account_id);
            if($tierApproval && $record->hv_result != 'green') {
                $tierApproval->update([
                    'status' => 'DECLINED'
                ]);
            }
            return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
        }
        $this->recordNotFound();
    }

}
