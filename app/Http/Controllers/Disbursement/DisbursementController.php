<?php

namespace App\Http\Controllers\Disbursement;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Disbursement\TransactionRequest;
use App\Services\Disbursement\IDisbursementDbpService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisbursementController extends Controller
{

    private IDisbursementDbpService $disbursementService;
    private IResponseService $responseService;

    public function __construct(
        IDisbursementDbpService $disbursementService,
        IResponseService $responseService
    ) {
        $this->disbursementService = $disbursementService;
        $this->responseService = $responseService;
    }

    /**
     * Transaction endpoint
     *
     * @return JsonResponse
     */
    public function transaction(TransactionRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $transaction = $this->disbursementService->transaction($request->user(), $fillRequest); 
        return $this->responseService->successResponse($transaction);
    }

}
