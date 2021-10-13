<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Services\KYCService\IKYCService;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\Merchant\MerchantSelfieVerificationRequest;
use App\Http\Requests\Merchant\MerchantToggleRequest;
use App\Http\Requests\Merchant\MerchantVerifyRequest;
use App\Services\Merchant\IMerchantService;

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

    public function list(Request $request) {
        $record = $this->merchatService->list($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }

    public function toggleMerchantStatus(MerchantToggleRequest $request) {
        $record = $this->merchatService->toggleMerchantStatus($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }

    public function verifyMerchant(MerchantVerifyRequest $request) {
        $record = $this->merchatService->verifyMerchant($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }

    public function showDocument(string $id) {
        $record = $this->merchatService->showDocument($id);
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }

    public function updateDocumentStatus(Request $request) {
        $record = $this->merchatService->updateDocumentStatus($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }
}
