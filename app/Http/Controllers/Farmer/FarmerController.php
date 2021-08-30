<?php

namespace App\Http\Controllers\Farmer;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\FarmerBatchUploadRequest;
use App\Http\Requests\Farmer\FarmerIdUploadRequest;
use App\Http\Requests\Farmer\FarmerSelfieUploadRequest;
use App\Http\Requests\Farmer\FarmerUpgradeToSilverRequest;
use App\Http\Requests\Farmer\FarmerVerificationRequest;
use App\Http\Requests\Farmer\FarmerVerificationUsingAccountNumberOnlyRequest;
use App\Jobs\Farmers\BatchUpload;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\FarmerProfile\IFarmerProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\Utilities\Verification\IVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    private IResponseService $responseService;
    private IFarmerProfileService $farmerProfileService;
    private IUserAccountRepository $userAccountRepository;
    private IVerificationService $verificationService;

    public function __construct(
        IResponseService       $responseService,
        IFarmerProfileService  $farmerProfileService,
        IUserAccountRepository $userAccountRepository,
        IVerificationService   $verificationService
    )
    {
        $this->responseService = $responseService;
        $this->farmerProfileService = $farmerProfileService;
        $this->userAccountRepository = $userAccountRepository;
        $this->verificationService = $verificationService;
    }

    public function farmerIdUpload(FarmerIdUploadRequest $request): JsonResponse
    {
        $record = $this->verificationService->create($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }

    public function farmerSelfieUpload(FarmerSelfieUploadRequest $request): JsonResponse
    {
        $record = $this->verificationService->createSelfieVerificationFarmers($request->all(), $request->user_account_id);
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }

    public function farmerVerification(FarmerVerificationRequest $request): JsonResponse
    {
        // $record = $this->farmerAccountService->getUserAccountByAccountNumberAndRSBSANo($request->all());
        $record = $this->userAccountRepository->getUserAccountByRSBSANo($request->rsbsa_number);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function farmerVerificationUserAccountNumberOnly(FarmerVerificationUsingAccountNumberOnlyRequest $request): JsonResponse
    {
        $record = $this->userAccountRepository->getUserByAccountNumberWithRelations($request->account_number);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function updateSilver(FarmerUpgradeToSilverRequest $request): JsonResponse
    {
        $record = $this->farmerProfileService->upgradeFarmerToSilver($request->all(), request()->user()->id);
        return $this->responseService->successResponse($record, SuccessMessages::updateUserSuccessful);
    }

    public function getFarmerViaRSVA(Request $request): JsonResponse
    {
        $record = $this->userAccountRepository->getUserByRSBAWithRelations($request->rsbsa_number);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function batchUpload(FarmerBatchUploadRequest $request): JsonResponse
    {
        $import = $this->farmerProfileService->batchUpload($request->file, request()->user()->id);

        return $this->responseService->successResponse($import, SuccessMessages::updateUserSuccessful);
    }

    public function processBatchUpload(FarmerBatchUploadRequest $request): JsonResponse
    {
        $this->dispatch(new BatchUpload($request->file, $request->user()->id));
        return $this->responseService->successResponse(null, SuccessMessages::processingRequestWithEmailNotification);
    }

    public function subsidyBatchUpload(FarmerBatchUploadRequest $request): JsonResponse
    {
        $import = $this->farmerProfileService->subsidyBatchUpload($request->file, request()->user()->id);

        return $this->responseService->successResponse($import, SuccessMessages::updateUserSuccessful);
    }
}
