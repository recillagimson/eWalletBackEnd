<?php

namespace App\Http\Controllers\UBP;

use App\Http\Controllers\Controller;
use App\Http\Requests\UBP\UbpAddMoneyRequest;
use App\Services\AddMoney\UBP\IUbpAddMoneyService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UBPAddmoneyController extends Controller
{
    private IUbpAddMoneyService $addMoneyService;
    private IResponseService $responseService;

    public function __construct(IUbpAddMoneyService $addMoneyService, IResponseService $responseService)
    {
        $this->addMoneyService = $addMoneyService;
        $this->responseService = $responseService;
    }

    // public function addMoney(UbpAddMoneyRequest $request): JsonResponse
    // {
    //     $data = $request->validated();
    //     $addMoney = $this->addMoneyService->addMoney($request->user()->id, $data['amount']);
    //     return $this->responseService->successResponse($addMoney->toArray());
    // }

    public function processPending(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $response = $this->addMoneyService->processPending($userId);
        return $this->responseService->successResponse($response);
    }
}
