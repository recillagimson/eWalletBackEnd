<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayBills\PayBillsRequest;
use App\Services\PayBills\IPayBillsService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;

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

    public function createPayment(PayBillsRequest $request)
    {
        $fillRequest = $request->validated();
        return $this->payBillsService->createPayment($request->user());
    }

}
