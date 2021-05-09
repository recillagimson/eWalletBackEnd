<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayBills\PayBillsRequest;
use App\Services\PayBills\IPayBillsService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Request;

class PayBillsController extends Controller
{
    private IPayBillsService $payBillsService;
    private IResponseService $responseService;

    public function __construct(IPayBillsService $payBillsService, IResponseService $responseService)
    {
        $this->payBillsService = $payBillsService;
        $this->responseService = $responseService;
    }
    
    public function getBillers(): JsonResponse
    {
        $billers = $this->payBillsService->getBillers();
        return $this->responseService->successResponse($billers);
    }

    public function getBillerInformation(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $billerInformation = $this->payBillsService->getBillerInformation($billerCode);
        return $this->responseService->successResponse($billerInformation);
    }

    public function getOtherCharges(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $otherCharges = $this->payBillsService->getOtherCharges($billerCode);
        return $this->responseService->successResponse($otherCharges);
    }

    public function createPayment(PayBillsRequest $request)
    {
        $fillRequest = $request->validated();
        return $this->payBillsService->createPayment($request->user());
    }

}
