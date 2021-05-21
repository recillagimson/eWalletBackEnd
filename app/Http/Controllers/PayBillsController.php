<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayBills\PayBillsRequest;
use App\Services\PayBills\IPayBillsService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Request;
use Illuminate\Http\Response;

class PayBillsController extends Controller
{
    private IPayBillsService $payBillsService;
    private IResponseService $responseService;

    public function __construct(IPayBillsService $payBillsService, IResponseService $responseService)
    {
        $this->payBillsService = $payBillsService;
        $this->responseService = $responseService;
    }


    /**
     * Gets biller request
     *
     * @param array $billers
     * @return JsonResponse
     */
    public function getBillers(): JsonResponse
    {
        $billers = $this->payBillsService->getBillers();
        return $this->responseService->successResponse($billers);
    }

    /**
     * Gets biller's information 
     *
     * @param PayBillsRequest $request
     * @param string $billerCode
     * @param array $billerInformation
     * @return JsonResponse
     */
    public function getBillerInformation(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $billerInformation = $this->payBillsService->getBillerInformation($billerCode);
        return $this->responseService->successResponse($billerInformation);
    }


    /**
     * Gets the Wallet Balance of SquidPay
     *
     * @param PayBillsRequest $request
     * @param array $getWalletBalance
     * @return JsonResponse
     */
    public function getWalletBalance(): JsonResponse
    {
        $getWalletBalance = $this->payBillsService->getWalletBalance();
        return $this->responseService->successResponse($getWalletBalance);
    }


    /**
     * Verify's the account reference number
     *
     * @param PayBillsRequest $request
     * @param string $billerCode
     * @param string $accountNumber
     * @param array $data
     * @param array $verifyAccount
     * @return JsonResponse
     */
    public function verifyAccount(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $accountNumber = $request->route('account_number');
        $data = $request->post();
        $verifyAccount = $this->payBillsService->verifyAccount($billerCode, $accountNumber, $data);
        return $this->responseService->successResponse($verifyAccount);
    }


    /**
     * Creates Payment  
     *
     * @param PayBillsRequest $request
     * @param string $billerCode
     * @param array $data
     * @param array $createPayment
     * @return JsonResponse
     */
    public function createPayment(PayBillsRequest $request)//: JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $data = $request->post();
        $createPayment = $this->payBillsService->createPayment($billerCode, $data,  $request->user());
        if(isset($createPayment['exception'])) return response()->json($createPayment, Response::HTTP_UNPROCESSABLE_ENTITY);
        return $this->responseService->successResponse($createPayment);
    }


    /**
     * Check the status of payment creation
     *
     * @param PayBillsRequest $request
     * @param string $billerCode
     * @param string $clientReference
     * @param array $inquirePayment
     * @return JsonResponse
     */
    public function inquirePayment(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $clientReference = $request->route('client_reference');
        $inquirePayment = $this->payBillsService->inquirePayment($billerCode, $clientReference);
        return $this->responseService->successResponse($inquirePayment);
    }

}
