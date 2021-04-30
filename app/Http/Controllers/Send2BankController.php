<?php

namespace App\Http\Controllers;

use App\Http\Requests\Send2Bank\FundTransferRequest;
use App\Http\Requests\Send2Bank\TransactionUpdateRequest;
use App\Services\Send2Bank\ISend2BankService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Send2BankController extends Controller
{
    private ISend2BankService $send2BankService;
    private IResponseService $responseService;

    public function __construct(ISend2BankService $send2BankService, IResponseService $responseService)
    {
        $this->send2BankService = $send2BankService;
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
     * Endpoint to transfer user funds to a bank account
     *
     * @param FundTransferRequest $request
     * @return JsonResponse
     */
    public function fundTransfer(FundTransferRequest $request): JsonResponse
    {
        $recipient = $request->validated();
        $userId = $request->user()->id;
        $response = $this->send2BankService->fundTransfer($userId, $recipient);
        return $this->responseService->successResponse($response);
    }

    public function processPending(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $response = $this->send2BankService->processPending($userId);
        return $this->responseService->successResponse($response);
    }

    public function updateTransaction(TransactionUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->send2BankService->updateTransaction($data['status'], $data['reference_number']);
        return $this->responseService->successResponse($response);
    }


}
