<?php

namespace App\Http\Controllers;

use App\Http\Requests\Send2Bank\FundTransferRequest;
use App\Services\Send2Bank\ISend2BankService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
        $recepient = $request->validated();
        $userId = $request->user()->id;
        $this->send2BankService->fundTransfer($userId, $recepient);
        return response()->json([], Response::HTTP_OK);
    }
}
