<?php

namespace App\Http\Controllers\Farmer;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\FarmerIdUploadRequest;
use App\Services\FarmerProfile\IFarmerProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\Farmer\FarmerSelfieUploadRequest;
use App\Http\Requests\Farmer\FarmerVerificationRequest;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Http\Requests\Farmer\FarmerUpgradeToSilverRequest;
use App\Services\Utilities\Verification\IVerificationService;
use App\Http\Requests\Farmer\FarmerVerificationUsingAccountNumberOnlyRequest;

class FarmerController extends Controller
{
    private IResponseService $responseService;
    private IFarmerProfileService $farmerProfileService;
    private IUserAccountRepository $userAccountRepository;
    private IVerificationService $verificationService;

    public function __construct(
        IResponseService $responseService,
        IFarmerProfileService $farmerProfileService,
        IUserAccountRepository $userAccountRepository,
        IVerificationService $verificationService
    )
    {
        $this->responseService = $responseService;
        $this->farmerProfileService = $farmerProfileService;
        $this->userAccountRepository = $userAccountRepository;
        $this->verificationService = $verificationService;
    }

    public function farmerIdUpload(FarmerIdUploadRequest $request) {
        $record = $this->verificationService->create($request->all());
        return $this->responseService->successResponse($record, SuccessMessages::success);
    }

    public function farmerSelfieUpload(FarmerSelfieUploadRequest $request) {
        $record = $this->verificationService->createSelfieVerification($request->all(), $request->user_account_id);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function farmerVerification(FarmerVerificationRequest $request) {
        $record = $this->userAccountRepository->getUserAccountByAccountNumberAndRSBSANo($request->account_number, $request->rsbsa_number);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function farmerVerificationUserAccountNumberOnly(FarmerVerificationUsingAccountNumberOnlyRequest $request) {
        $record = $this->userAccountRepository->getUserByAccountNumberWithRelations($request->account_number);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function updateSilver(FarmerUpgradeToSilverRequest $request)
    {
        $record = $this->farmerProfileService->upgradeFarmerToSilver($request->all(), request()->user()->id);
        return $this->responseService->successResponse($record, SuccessMessages::updateUserSuccessful);
    }
    
    public function getFarmerViaRSVA(Request $request) {
        $record = $this->userAccountRepository->getUserByRSBAWithRelations($request->rsbsa_number);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }
}
