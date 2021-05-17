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


    public function getRequiredFields(PayBillsRequest $request) : JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $requiredFields = $this->payBillsService->getRequiredFields($billerCode);
        return $this->responseService->successResponse($requiredFields);
    }


    public function getOtherCharges(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $otherCharges = $this->payBillsService->getOtherCharges($billerCode);
        return $this->responseService->successResponse($otherCharges);
    }


    public function getWalletBalance(): JsonResponse
    {
        $getWalletBalance = $this->payBillsService->getWalletBalance();
        return $this->responseService->successResponse($getWalletBalance);
    }


    public function verifyAccount(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $accountNumber = $request->route('account_number');
        $data = $request->post();
        $verifyAccount = $this->payBillsService->verifyAccount($billerCode, $accountNumber, $data);
        
        return $this->responseService->successResponse($verifyAccount);
    }
    

    public function createPayment(PayBillsRequest $request)//: JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $data = $request->post();
        
        return $this->payBillsService->createPayment($billerCode, $data,  $request->user());
      //  return $this->responseService->successResponse($createPayment);
    }


    public function inquirePayment(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $clientReference = $request->route('client_reference');
        $inquirePayment = $this->payBillsService->inquirePayment($billerCode, $clientReference);
        return $this->responseService->successResponse($inquirePayment);
    }

}
