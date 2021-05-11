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
    
    
    public function getBillers()//: JsonResponse
    {
        $billers = $this->payBillsService->getBillers();
        //return $this->responseService->successResponse($billers);
        return $billers;
    }


    public function getBillerInformation(PayBillsRequest $request)//: JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $billerInformation = $this->payBillsService->getBillerInformation($billerCode);
        // return $this->responseService->successResponse($billerInformation);
        return $billerInformation;
    }


    public function getOtherCharges(PayBillsRequest $request)//: JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $otherCharges = $this->payBillsService->getOtherCharges($billerCode);
       // return $this->responseService->successResponse($otherCharges);
        return $otherCharges;
    }


    public function getWalletBalance()//: JsonResponse
    {
        $getWalletBalance = $this->payBillsService->getWalletBalance();
        //return $this->responseService->successResponse($getWalletBalance);
        return $getWalletBalance; 
    }


    public function verifyAccount(PayBillsRequest $request)//: JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $accountNumber = $request->route('account_number');

        $data = $request->post();
        $verifyAccount = $this->payBillsService->verifyAccount($billerCode, $accountNumber, $data);
         
        return $verifyAccount;
    }
    

    public function createPayment(PayBillsRequest $request)
    {
        $billerCode = $request->route('biller_code');

        $data = $request->post();
        $createPayment = $this->payBillsService->createPayment($billerCode, $data);

        return $createPayment;
    }


    public function inquirePayment(PayBillsRequest $request)
    {
        $billerCode = $request->route('biller_code');
        $clientReference = $request->route('client_reference');

        $inquirePayment = $this->payBillsService->inquirePayment($billerCode, $clientReference);
        return $inquirePayment;
    }

}
