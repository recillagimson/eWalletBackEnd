<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\SuccessMessages;
use App\Http\Requests\UserPhoto\SelfieUploadRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserPhoto\VerificationRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\Utilities\Verification\IVerificationService;

class UserPhotoController extends Controller
{
    private IVerificationService $iVerificationService;
    private IResponseService $responseService;


    public function __construct(IResponseService $responseService,
                                IVerificationService $iVerificationService)
    {
        $this->responseService = $responseService;
        $this->iVerificationService = $iVerificationService;
    }


    public function createVerification(VerificationRequest $request) {
        $params = $request->all();
        $params['user_account_id'] = request()->user()->id;
        $createRecord = $this->iVerificationService->create($params);
        // $encryptedResponse = $this->encryptionService->encrypt(array($createRecord));
        // return response()->json($encryptedResponse, Response::HTTP_OK);
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::success);
    }

    public function createSelfieVerification(SelfieUploadRequest $request) {
        $params = $request->all();
        $createRecord = $this->iVerificationService->createSelfieVerification($params);
        // $encryptedResponse = $this->encryptionService->encrypt(array($createRecord));
        // return response()->json($encryptedResponse, Response::HTTP_OK);
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::success);
    }
}
