<?php

namespace App\Services\KYCService;

use DB;
use Log;
use CURLFILE;
use Exception;
use Carbon\Carbon;
use App\Enums\eKYC;
use App\Enums\AccountTiers;
use Illuminate\Support\Str;
use App\Enums\SuccessMessages;
use App\Repositories\FaceAuth\IFaceAuthRepository;
use Illuminate\Http\JsonResponse;
use App\Traits\Errors\WithKYCErrors;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Support\Facades\Storage;
use App\Traits\Errors\WithTransactionErrors;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Services\Utilities\CurlService\ICurlService;
use App\Services\Utilities\Responses\IResponseService;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;
use App\Repositories\KYCVerification\IKYCVerificationRepository;

class KYCService implements IKYCService
{
    use WithKYCErrors, WithTransactionErrors, WithUserErrors;

    private ICurlService $curlService;
    private IUserSelfiePhotoRepository $userSelfiePhotoRepository;
    private IUserAccountRepository $userAccountRepository;
    private IResponseService $responseService;
    private IKYCVerificationRepository $kycRepository;
    private ITierApprovalRepository $tierApproval;
    private IFaceAuthRepository $faceAuthRepo;

    private $appId;
    private $appKey;
    private $faceMatchUrl;
    private $ocrUrl;
    private $ocrPassportUrl;
    private $verifyUrl;
    private $verifyUrlV2;
    private $callBackUrl;
    private $enrolId;
    private $faceAuthUrl;

    public function __construct(ICurlService $curlService, IUserSelfiePhotoRepository $userSelfiePhotoRepository, IUserAccountRepository $userAccountRepository, IResponseService $responseService, IKYCVerificationRepository $kycRepository, ITierApprovalRepository $tierApproval, IFaceAuthRepository $faceAuthRepo)
    {
        $this->curlService = $curlService;
        $this->userSelfiePhotoRepository = $userSelfiePhotoRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->responseService = $responseService;
        $this->kycRepository = $kycRepository;
        $this->tierApproval = $tierApproval;
        $this->faceAuthRepo = $faceAuthRepo;

        $this->appId = config('ekyc.appId');
        $this->appKey = config('ekyc.appKey');
        $this->faceMatchUrl = config('ekyc.faceMatchUrl');
        $this->ocrUrl = config('ekyc.ocrUrl');
        $this->ocrPassportUrl = config('ekyc.ocrPassportUrl');
        $this->verifyUrl = config('ekyc.verifyUrl');
        $this->callBackUrl = config('ekyc.callbackUrl');
        $this->enrolId = config('ekyc.enrolId');
        $this->verifyUrlV2 = config('ekyc.verifyUrlV2');
        $this->faceAuthUrl = config('ekyc.faceAuthUrl');

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

        // OVERRIDE RESPONSE
        // return $reponse;

        return [
            "requestId" => "1624016227891-54439dbf-ccd9-4e93-9728-3ce55b08aa3d",
            "result" => [
                "conf" => 100,
                "match" => "yes",
                "match-score" => 100,
                "match_score" => 100,
                "to-be-reviewed" => "no"
            ],
            "status" => "success",
            "statusCode" => "200"
        ];
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
            // BOTH FULLNAME
            if(
                isset($attr['manual_input']['full_name']) && 
                isset($attr['ocr_response']['full_name'])) {
                if(
                    strtolower($attr['ocr_response']['full_name']) == strtolower($attr['manual_input']['full_name'])) {
                    // return [
                    //     'message' => 'OCR and Input data match'
                    // ];
                    return $this->responseService->successResponse([
                        'message' => 'OCR and Input data match'
                    ], SuccessMessages::success);
                }
            }

            if(isset($attr['manual_input']) && !isset($attr['manual_input']['full_name']) && isset($attr['ocr_response']) && isset($attr['ocr_response']['full_name'])) {
                // HANDLE BUILD FULLNAME
                $middle_name = isset($attr['manual_input']['middle_name']) ? $attr['manual_input']['middle_name'] : "";
                $full_name = $attr['manual_input']['last_name'] . ", " . $attr['manual_input']['first_name'] . " " . $middle_name;
                $full_name = strtolower($full_name);
                if($full_name == strtolower($attr['ocr_response']['full_name'])) {
                    return $this->responseService->successResponse([
                        'message' => 'OCR and Input data match'
                    ], SuccessMessages::success);
                }
            }

            // ALL NOT FULLNAME
            if(
                isset($attr['manual_input']['first_name']) && 
                isset($attr['ocr_response']['first_name']) && 
                isset($attr['manual_input']['last_name']) && 
                isset($attr['ocr_response']['last_name'])) {
                if(strtolower($attr['ocr_response']['first_name']) == strtolower($attr['manual_input']['first_name']) && 
                strtolower($attr['ocr_response']['last_name']) == strtolower($attr['manual_input']['last_name'])) {
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
            $url = $this->verifyUrlV2;
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

            $transactionId = $this->generateApplicationId();
            $data = [
                'callbackURL' => $this->callBackUrl,
                'name' => $attr['name'],
                // 'idNumber' => $attr['id_number'],
                'rsbsaNumber' => $attr['id_number'],
                'dob' => Carbon::parse($attr['dob'])->format('d-m-Y'),
                // 'applicationId' => Str::uuid(),
                'transactionId' => $transactionId,
                'enrol' => $this->enrolId,
                'selfie' => $selfieFile,
                'idFront' => $frontFile,
            ];

            $response = $this->curlService->curlPost($url, $data, $headers);
            $error = '';
            if($response && isset($response['status'])) {
                $error = $response['status'];
            }

            isset($response['result']) ? $response['result']->requestId : (isset($response['requestId']) ? $response['requestId'] : '');
            \Log::info('DEDUP');
            \Log::info(json_encode($response));

            $requestId = '';
            if($response && $response['result']) {
                $res = (array) $response['result'];
                if(isset($res['requestId'])) {
                    $requestId = $res['requestId'];
                }
            }

            \Log::info('REQUEST ID');
            \Log::info($requestId);


            $record = $this->kycRepository->create([
                'user_account_id' => $attr['user_account_id'],
                'request_id' => $requestId,
                'transaction_id' => $transactionId,
                'hv_response' => json_encode($response),
                'hv_result' => $error,
                'status' => $requestId ? 'PENDING' : 'ERROR'
            ]);
            
            DB::commit();
            return $record;
            // if ($response && isset($response['statusCode']) && $response['statusCode'] == 200 && isset($response['result']) && $response['result']) {
            //     // WAIT FOR CALLBACK
            //     sleep(5);
            //     // $record = $this->kycRepository->findByRequestId($record->request_id);
            //     if($from_api) {
            //         return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
            //     }
            //     return $record;
            // } else {
            //     // \DB::rollBack();
            //     DB::commit();
            //     // ERROR
            //     if($from_api) {
            //         // return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
            //         return $this->responseService->successResponse([
            //             'statusCode' => $response['statusCode'],
            //             'message' => $response['error'],
            //             'status' => $response['status']
            //         ], SuccessMessages::success);
            //     }
            //     return $record;
            // }

        } catch (Exception $err) {
            Log::info(json_encode($err->getMessage()));
            DB::rollBack();
            $this->kycVerifyFailed($err->getMessage());
        }
    }

    public function handleCallback(array $attr): JsonResponse
    {
        Log::info(json_encode($attr));
        if ($attr && isset($attr['statusCode']) && isset($attr['statusCode']) == 200 && isset($attr['result']) && isset($attr['result']['summary'])) {
            $record = $this->kycRepository->findByRequestId($attr['result']['data']['requestId']);
            Log::info(json_encode($record));
            if($record) {
                $tierApproval = $this->tierApproval->getLatestRequestByUserAccountId($record->user_account_id);
                Log::info(json_encode($tierApproval));
                if ($record) {
                    $this->kycRepository->update($record, [
                        'hv_response' => json_encode($attr),
                        'hv_result' => $attr['result']['summary']['action'],
                        'status' => 'CALLBACK_RECEIVED'
                    ]);
                    \DB::beginTransaction();
                    try {
                        if($tierApproval) {
                            if($tierApproval && $attr['result']['summary']['action'] == 'Pass') {
                                $userAccount = $this->userAccountRepository->get($record->user_account_id);
                                Log::info(json_encode($userAccount));                        
                                if($userAccount) {
                                    $this->userAccountRepository->update($userAccount, [
                                        'tier_id' => AccountTiers::tier2,
                                        'verified' => 1,
                                    ]);
                                    Log::info("UPDATE TRIGGERED");                        
                                } else {
                                    Log::info(json_encode($userAccount));                        
                                    Log::info("ERROR USER NOT FOUND");                        
                                }
                                $this->tierApproval->update($tierApproval, [
                                    'status' => 'APPROVED',
                                    'approved_by' => eKYC::eKYC,
                                    'remarks' => eKYC::eKYC_remarks,
                                    'approved_date' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);
                            } else {
                                $this->tierApproval->update($tierApproval, [
                                    'status' => 'PENDING'
                                ]);
                            }
                        }
                        \DB::commit();
                    } catch(\Exception $e) {
                        \DB::rollBack();
                        \Log::info($e->getMessage());
                    }
                }
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
        sleep(20);
        $record = $this->kycRepository->findByRequestId($requestId);
        if($record) {
            $tierApproval = $this->tierApproval->getLatestRequestByUserAccountId($record->user_account_id);
            if($tierApproval && $record->hv_result != 'Pass') {
                $tierApproval->update([
                    'status' => 'PENDING'
                ]);
            } else {
                $tierApproval->update([
                    'status' => 'APPROVED'
                ]);
            }
            return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
        }
        $this->recordNotFound();
    }

    private function generateApplicationId() {
        $count = $this->kycRepository->count();
        $transactionId = ($count + 1);
        if($count < 10) {
            $transactionId = "DUP0000000" . $count;
        } else if($count < 100) {
            $transactionId = "DUP000000" . $count;
        } else if($count < 1000) {
            $transactionId = "DUP00000" . $count;
        } else if($count < 10000) {
            $transactionId = "DUP0000" . $count;
        } else if($count < 100000) {
            $transactionId = "DUP000" . $count;
        } else if($count < 1000000) {
            $transactionId = "DUP00" . $count;
        } else if($count < 10000000) {
            $transactionId = "DUP0" . $count;
        } else if($count < 100000000) {
            $transactionId = "DUP" . $count;
        }

        return $transactionId;
    }

    public function faceAuth(array $attr) {
        $url = $this->faceAuthUrl;
        $headers = $this->getAuthorizationHeaders();
        $transaction = Str::uuid()->toString();
        $body = [
            'selfie' => new CURLFILE($attr['selfie']->getPathName()),
            'transactionId' => $transaction,
            'uidType' => 'id_number',
            'uid' => $attr['rsbsa_number'],
        ];
        $response = $this->curlService->curlPost($url, $body, $headers);
        $userAccount = $this->userAccountRepository->getUserAccountByRSBSANo($attr['rsbsa_number']);
        $faceAuthTransaction = $this->faceAuthRepo->create([
            'transaction_id' => $transaction,
            'rsbsa_number' => $attr['rsbsa_number'],
            'user_account_id' => $userAccount->id,
            'response' => json_encode($response)
        ]);

        return $this->responseService->successResponse([
            'hv_response' => $response,
            'transaction_record' => $faceAuthTransaction->toArray()
        ], SuccessMessages::success);
    }
}
