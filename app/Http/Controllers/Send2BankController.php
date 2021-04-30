<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Send2Bank\ISend2BankService;
use App\Services\Send2Bank\ISend2BankDirectService;
use App\Http\Requests\Send2Bank\FundTransferRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\Send2Bank\Send2BankUBPDirectRequest;

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
     * Endpoint to transfer user funds to a bank account
     *
     * @param FundTransferRequest $request
     * @return JsonResponse
     */
    public function fundTransfer(FundTransferRequest $request): JsonResponse
    {
        $recipient = $request->validated();
        $userId = $request->user()->id;
        $this->send2BankService->fundTransfer($userId, $recipient);
        return response()->json([], Response::HTTP_OK);
    }

    public function send2BankUBPDirect(Send2BankUBPDirectRequest $request) : JsonResponse {
        $recipient = $request->all();
        $userId = $request->user()->id;
        $this->send2BankDirectService->fundTransferToUBPDirect($userId, $recipient);
        return response()->json([], Response::HTTP_OK);
    }
}
