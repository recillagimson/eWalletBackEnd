<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Services\KYCService\IKYCService;
use App\Services\Merchant\IMerchantService;
use App\Http\Requests\Merchant\MerchantToggleRequest;
use App\Http\Requests\Merchant\MerchantVerifyRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\MerchantAccount\IMerchantAccountService;
use App\Http\Requests\Merchant\CreateMerchantAccountRequest;
use App\Http\Requests\Merchant\UpdateMerchantAccountRequest;
use App\Http\Requests\Merchant\SetUserMerchantAccountRequest;
use App\Repositories\MerchantAccount\IMerchantAccountRepository;
use App\Http\Requests\Merchant\MerchantSelfieVerificationRequest;

class MerchantController extends Controller
{
    private IResponseService $responseService;
    private IKYCService $kycService;
    private IMerchantService $merchatService;
    private IMerchantAccountRepository $merchantAccountRepo;
    private IMerchantAccountService $merchantAccountService;

    public function __construct(
        IResponseService $responseService,
        IKYCService $kycService,
        IMerchantService $merchatService,
        IMerchantAccountRepository $merchantAccountRepo,
        IMerchantAccountService $merchantAccountService
    )
    {
        $this->responseService = $responseService;
        $this->kycService = $kycService;
        $this->merchatService = $merchatService;
        $this->merchantAccountRepo = $merchantAccountRepo;
        $this->merchantAccountService = $merchantAccountService;
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


    // DBP MERCHANT
    public function listMerchantAccount() {
        $records = $this->merchantAccountRepo->getAll();
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    public function storeMerchantAccount(CreateMerchantAccountRequest $request) {
        $attr = $request->all();
        $attr['created_by'] = request()->user()->id;
        $attr['updated_by'] = request()->user()->id;
        $attr['merchant_balance'] = 0;
        $record = $this->merchantAccountService->create($attr);
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }

    public function updateMerchantAccount(UpdateMerchantAccountRequest $request) {
        $attr = $request->all();
        $merchantAccount = $this->merchantAccountRepo->get($request->merchant_account_id);
        $attr['updated_by'] = request()->user()->id;
        $this->merchantAccountRepo->update($merchantAccount, $attr);
        $merchantAccount = $this->merchantAccountRepo->get($request->merchant_account_id);
        return $this->responseService->successResponse($merchantAccount->toArray(), SuccessMessages::success);
    }

    public function setUserMerchantAccount(SetUserMerchantAccountRequest $request) {
        $records = $this->merchantAccountService->setUserMerchantAccount($request->all());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }
}
