<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use App\Enums\TransactionCategoryIds;
use App\Http\Requests\BPI\BPIOTPRequest;
use App\Services\BPIService\IBPIService;
use App\Http\Requests\BPI\BPIAuthRequest;
use App\Http\Requests\BPI\BPIStatusRequest;
use App\Http\Requests\BPI\BPIProcessRequest;
use App\Http\Requests\BPI\BPIFundTopUpRequest;
use App\Http\Requests\BPI\BPIGetAccountRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\Transaction\ITransactionValidationService;
use App\Http\Requests\BPI\BPITransactionLimitValidationRequest;

class BPIController extends Controller
{

    private IBPIService $bpiService;
    private IResponseService $responseService;
    private ITransactionValidationService $transactionValidationService;

    public function __construct(IBPIService $bpiService, IResponseService $responseService, ITransactionValidationService $transactionValidationService)
    {
        $this->bpiService = $bpiService;
        $this->responseService = $responseService;
        $this->transactionValidationService = $transactionValidationService;
    }

    public function bpiAuth(BPIAuthRequest $request): JsonResponse
    {
        $response = $this->bpiService->bpiAuth($request->code);
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }

    public function getAccounts(BPIGetAccountRequest $request): JsonResponse
    {
        $response = $this->bpiService->getAccounts($request->token);
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }

    public function fundTopUp(BPIFundTopUpRequest $request): JsonResponse
    {
        $data = [
            'accountNumberToken' => $request->accountNumberToken,
            'amount' => $request->amount,
            'remarks' => $request->remarks
        ];
        $response = $this->bpiService->fundTopUp($data, $request->token);
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }

    public function otp(BPIOTPRequest $request): JsonResponse
    {
        $response = $this->bpiService->otp($request->all());
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }

    public function process(BPIProcessRequest $request): JsonResponse
    {
        $response = $this->bpiService->process($request->all(), request()->user()->id);
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }

    public function status(BPIStatusRequest $request): JsonResponse
    {
        $response = $this->bpiService->status($request->all());
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }

    public function getBPIAuthUrl(BPITransactionLimitValidationRequest $request) {
        $this->transactionValidationService->checkUserMonthlyTransactionLimit(request()->user(), $request->amount, TransactionCategoryIds::cashinBPI);
        return $this->responseService->successResponse(
            [
                'login_url' => config('bpi.loginUrl')
            ], 
            SuccessMessages::success
        );
    }

    public function getCredentials() {
        return $this->responseService->successResponse(
            [
                'clientId' => config('bpi.clientId'),
                'clientSecret' => config('bpi.clientSecret')
            ], 
            SuccessMessages::success
        );
    }
}
