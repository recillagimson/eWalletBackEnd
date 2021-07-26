<?php

namespace App\Http\Controllers\KYC;

use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\KYC\OCRRequest;
use Illuminate\Support\Facades\Storage;
use App\Services\KYCService\IKYCService;
use App\Http\Requests\KYC\FaceMatchRequest;
use League\CommonMark\Inline\Element\Image;
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
}
