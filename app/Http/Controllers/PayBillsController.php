<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\PayBills\PayBillsRequest;
use App\Http\Requests\PayBills\ValidateAccountRequest;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Services\PayBills\IPayBillsService;
use App\Services\Utilities\PDF\IPDFService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;

class PayBillsController extends Controller
{
    private IPayBillsService $payBillsService;
    private IResponseService $responseService;
    private IOutPayBillsRepository $outPayBillsRepository;
    private IPDFService $pdfService;

    public function __construct(IPayBillsService $payBillsService, IResponseService $responseService,
                                IOutPayBillsRepository $outPayBillsRepository, IPDFService $pdfService)
    {
        $this->payBillsService = $payBillsService;
        $this->responseService = $responseService;
        $this->outPayBillsRepository = $outPayBillsRepository;
        $this->pdfService = $pdfService;
    }


    /**
     * Gets biller request
     *
     * @return JsonResponse
     */
    public function getBillers(): JsonResponse
    {
        $billers = $this->payBillsService->getBillers();
        if (isset($billers['provider_error'])) return $this->responseService->tpaErrorReponse($billers);
        return $this->responseService->successResponse($billers);
    }

    /**
     * Gets biller's information
     *
     * @param PayBillsRequest $request
     * @return JsonResponse
     */
    public function getBillerInformation(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $billerInformation = $this->payBillsService->getBillerInformation($billerCode);
        if (isset($billerInformation['provider_error'])) return $this->responseService->tpaErrorReponse($billerInformation);
        return $this->responseService->successResponse($billerInformation);
    }


    /**
     * Gets the Wallet Balance of SquidPay
     *
     * @return JsonResponse
     */
    public function getWalletBalance(): JsonResponse
    {
        $getWalletBalance = $this->payBillsService->getWalletBalance();
        if (isset($getWalletBalance['provider_error'])) return $this->responseService->tpaErrorReponse($getWalletBalance);
        return $this->responseService->successResponse($getWalletBalance);
    }


    /**
     * Verify's the account reference number
     *
     * @param PayBillsRequest $request
     * @return JsonResponse
     */
    public function validateAccount(ValidateAccountRequest $request)//: JsonResponse
    {
        $data = $request->validated();
        $billerCode = $request->route('biller_code');
        $accountNumber = $data['account_number'];

        $verifyAccount = $this->payBillsService->validateAccount($billerCode, $accountNumber, $data, $request->user());
        return $this->responseService->successResponse($verifyAccount, SuccessMessages::transactionValidationSuccessful);
    }

     
    /**
     * Creates Payment
     *
     * @param PayBillsRequest $request
     * @return JsonResponse
     */
    public function createPayment(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $data = $request->post();
        $createPayment = $this->payBillsService->createPayment($billerCode, $data,  $request->user());
        if (isset($createPayment['provider_error'])) return $this->responseService->tpaErrorReponse($createPayment);
        return $this->responseService->successResponse($createPayment);
    }


    /**
     * Check the status of payment creation
     *
     * @param PayBillsRequest $request
     * @return JsonResponse
     */
    public function inquirePayment(PayBillsRequest $request): JsonResponse
    {
        $billerCode = $request->route('biller_code');
        $clientReference = $request->route('client_reference');
        $inquirePayment = $this->payBillsService->inquirePayment($billerCode, $clientReference);
        if (isset($inquirePayment['provider_error'])) return $this->responseService->tpaErrorReponse($inquirePayment);
        return $this->responseService->successResponse($inquirePayment);
    }


    public function processPending(PayBillsRequest $request): JsonResponse
    {
        $response = $this->payBillsService->processPending($request->user());
        return $this->responseService->successResponse($response);
    }

    /**
     * List of billers
     *
     * @return JsonResponse
     */
    public function getListOfBillers(): JsonResponse
    {
        $response = $this->outPayBillsRepository->getAllBillersWithPaginate();
        return $this->responseService->successResponse($response->toArray());
    }

    public function downloadListOfBillersPDF()
    {
        $getAllBillers = $this->outPayBillsRepository->getAllBillers();
        $data = [
            'datas' => $getAllBillers,
        ];

        return $this->pdfService->generatePDFNoUserPassword($data, 'reports.out_pay_bills_history.out_pay_bills_history');
    }

    public function downloadListOfBillersCSV()
    {
        return $this->payBillsService->downloadListOfBillersCSV();
    }

}
