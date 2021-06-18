<?php

namespace App\Http\Controllers\Farmer;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Services\FarmerProfile\IFarmerProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\Farmer\FarmerUpgradeToSilverRequest;
use Request;

class FarmerController extends Controller
{
    private IResponseService $responseService;
    private IFarmerProfileService $farmerProfileService;

    public function __construct(
        IResponseService $responseService,
        IFarmerProfileService $farmerProfileService
    )
    {
        $this->responseService = $responseService;
        $this->farmerProfileService = $farmerProfileService;
    }

    public function updateSilver(FarmerUpgradeToSilverRequest $request)
    {
        $record = $this->farmerProfileService->upgradeFarmerToSilver($request->all(), request()->user()->id);
        return $this->responseService->successResponse($record, SuccessMessages::updateUserSuccessful);
    }
}
