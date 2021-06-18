<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Services\KYCService\IKYCService;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\Merchant\MerchantSelfieVerificationRequest;

class MerchantController extends Controller
{
    private IResponseService $responseService;
    private IKYCService $kycService;

    public function __construct(
        IResponseService $responseService,
        IKYCService $kycService
    )
    {
        $this->responseService = $responseService;
        $this->kycService = $kycService;
    }

    public function selfieVerification(MerchantSelfieVerificationRequest $request) {
        $record = $this->kycService->initMerchantFaceMatch($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }
}
