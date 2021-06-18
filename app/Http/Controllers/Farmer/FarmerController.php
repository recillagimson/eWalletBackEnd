<?php

namespace App\Http\Controllers\Farmer;

use Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Services\FarmerProfile\IFarmerProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\Farmer\FarmerVerificationRequest;
use App\Http\Requests\Farmer\FarmerUpgradeToSilverRequest;
use App\Repositories\UserAccount\IUserAccountRepository;

class FarmerController extends Controller
{
    private IResponseService $responseService;
    private IFarmerProfileService $farmerProfileService;
    private IUserAccountRepository $userAccountRepository;

    public function __construct(
        IResponseService $responseService,
        IFarmerProfileService $farmerProfileService,
        IUserAccountRepository $userAccountRepository
    )
    {
        $this->responseService = $responseService;
        $this->farmerProfileService = $farmerProfileService;
        $this->userAccountRepository = $userAccountRepository;
    }

    public function farmerVerification(FarmerVerificationRequest $request) {
        $record = $this->userAccountRepository->getUserAccountByIdAndRSBSANo($request->user_account_id, $request->rsbsa_number);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function updateSilver(FarmerUpgradeToSilverRequest $request)
    {
        $record = $this->farmerProfileService->upgradeFarmerToSilver($request->all(), request()->user()->id);
        return $this->responseService->successResponse($record, SuccessMessages::updateUserSuccessful);
    }
}
