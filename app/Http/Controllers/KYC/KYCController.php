<?php

namespace App\Http\Controllers\KYC;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\KYC\OCRRequest;
use App\Services\KYCService\IKYCService;
use App\Http\Requests\KYC\FaceAuthRequest;
use App\Http\Requests\KYC\MatchOCRRequest;
use App\Http\Requests\KYC\FaceMatchRequest;
use App\Http\Requests\KYC\ExpirationCheckRequest;
use App\Services\Utilities\Responses\IResponseService;

class KYCController extends Controller
{

    private IKYCService $kycService;
    private IResponseService $responseService;

    public function __construct(IKYCService $kycService, IResponseService $responseService)
    {
        $this->kycService = $kycService;
        $this->responseService = $responseService;
    }

    public function initFaceMatch(FaceMatchRequest $request) {
        $response =  $this->kycService->initFaceMatch($request->all());
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }

    public function initOCR(OCRRequest $request) {
        $response =  $this->kycService->initOCR($request->all());
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }

    public function checkIDExpiration(ExpirationCheckRequest $request) {
        $response =  $this->kycService->checkIDExpiration($request->all(), $request->id_type);
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }

    public function matchOCR(MatchOCRRequest $request) {
        return $this->kycService->matchOCR($request->all());
    }

    public function callback(Request $request) {
        return $this->kycService->handleCallback($request->all());
    }

    public function verifyRequest(string $requestId) {
        return $this->kycService->verifyRequest($requestId);
    }

    public function faceAuth(FaceAuthRequest $request) {
        return $this->kycService->faceAuth($request->all());
    }
}
