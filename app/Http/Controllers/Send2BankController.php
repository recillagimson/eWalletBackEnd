<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\Send2Bank\FundTransferRequest;
use App\Http\Requests\Send2Bank\TransactionUpdateRequest;
use App\Services\Send2Bank\ISend2BankService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

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
     * Validates user qualification for fund transfer
     *
     * @param FundTransferRequest $request
     * @return JsonResponse
     */
    public function validateFundTransfer(FundTransferRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $recipient = $request->validated();
        $this->send2BankService->validateFundTransfer($userId, $recipient);

        return $this->responseService->successResponse(null,
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


}
