<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\Send2Bank\FundTransferRequest;
use App\Http\Requests\Send2Bank\Send2BankUBPDirectRequest;
use App\Http\Requests\Send2Bank\TransactionUpdateRequest;
use App\Services\Send2Bank\ISend2BankDirectService;
use App\Services\Send2Bank\ISend2BankService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Send2BankController extends Controller
{
    private ISend2BankService $send2BankService;
    private ISend2BankDirectService $send2BankDirectService;
    private IResponseService $responseService;

    public function __construct(ISend2BankService $send2BankService, IResponseService $responseService, ISend2BankDirectService $send2BankDirectService)
    {
        $this->send2BankService = $send2BankService;
        $this->send2BankDirectService = $send2BankDirectService;
        $this->responseService = $responseService;
    }

    /**
     * Endpoint to retrieve list of pesonet supported banks
     *
     * @return JsonResponse
     */
    public function getBanks(): JsonResponse
    {
        $banks = $this->send2BankService->getBanks();
        return $this->responseService->successResponse($banks);
    }

    /**
     * Endpoint to retrieve list of instapay transaction purposes
     *
     * @return JsonResponse
     */
    public function getPurposes(): JsonResponse
    {
        $purposes = $this->send2BankService->getPurposes();
        return $this->responseService->successResponse($purposes);
    }

    /**
     * Validates user qualification for fund transfer
     *
     * @param FundTransferRequest $request
     * @return JsonResponse
     */
    public function validateFundTransfer(FundTransferRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $recipient = $request->validated();
        $response = $this->send2BankService->validateFundTransfer($userId, $recipient);

        return $this->responseService->successResponse($response,
            SuccessMessages::transactionValidationSuccessful);
    }

    /**
     * Endpoint to transfer user funds to a bank account
     *
     * @param FundTransferRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function fundTransfer(FundTransferRequest $request): JsonResponse
    {
        $recipient = $request->validated();
        $userId = $request->user()->id;
        $response = $this->send2BankService->fundTransfer($userId, $recipient);
        return $this->responseService->successResponse($response);
    }

    /**
     * Endpoint to check updates on pending transactions and process them accordingly
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function processPending(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $response = $this->send2BankService->processPending($userId);
        return $this->responseService->successResponse($response);
    }

    /**
     * Endpoint to manually update transaction status. For testing purposes only.
     *
     * @param TransactionUpdateRequest $request
     * @return JsonResponse
     */
    public function updateTransaction(TransactionUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->send2BankService->updateTransaction($data['status'], $data['reference_number']);
        return $this->responseService->successResponse($response);
    }

    /**
     * Endpoint for Send 2 Bank directly to UBP account
     *
     * @param Send2BankUBPDirectRequest $request
     * @return JsonResponse
     */
    public function send2BankUBPDirect(Send2BankUBPDirectRequest $request) : JsonResponse {
        $recipient = $request->all();
        $userId = $request->user()->id;
        $this->send2BankDirectService->fundTransferToUBPDirect($userId, $recipient);
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * Endpoint for verification Send 2 Bank directly to UBP account transactions
     *
     * @param null
     * @return JsonResponse
     */
    public function verifyDirectTransactions() : JsonResponse {
        $this->send2BankDirectService->verifyPendingDirectTransactions(request()->user()->id);
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * Validates user qualification for fund transfer direct to UBP
     *
     * @param Send2BankUBPDirectRequest $request
     * @return JsonResponse
     */
    public function validateFundTransferDirectUBP(Send2BankUBPDirectRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $recipient = $request->validated();
        $this->send2BankDirectService->validateFundTransfer($userId, $recipient);

        return $this->responseService->successResponse(null,
            SuccessMessages::transactionValidationSuccessful);
    }
}
