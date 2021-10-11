<?php

namespace App\Http\Controllers\KYC;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\KYC\ExpirationCheckRequest;
use App\Http\Requests\KYC\FaceMatchRequest;
use App\Http\Requests\KYC\MatchOCRRequest;
use App\Http\Requests\KYC\OCRRequest;
use App\Services\KYCService\IKYCService;
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
}
