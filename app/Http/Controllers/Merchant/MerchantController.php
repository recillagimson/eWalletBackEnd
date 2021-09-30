<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Services\KYCService\IKYCService;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\Merchant\MerchantSelfieVerificationRequest;
use App\Services\Merchant\IMerchantService;

class MerchantController extends Controller
{
    private IResponseService $responseService;
    private IKYCService $kycService;
    private IMerchantService $merchatService;

    public function __construct(
        IResponseService $responseService,
        IKYCService $kycService,
        IMerchantService $merchatService
    )
    {
        $this->responseService = $responseService;
        $this->kycService = $kycService;
        $this->merchatService = $merchatService;
    }

    public function selfieVerification(MerchantSelfieVerificationRequest $request) {
        $record = $this->kycService->initMerchantFaceMatch($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }

    public function list(Request $request) {
        $record = $this->merchatService->list($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }
}
