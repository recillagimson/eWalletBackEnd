<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Services\BPIService\IBPIService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\Request;

class BPIController extends Controller
{

    private IBPIService $bpiService;
    private IResponseService $responseService;

    public function __construct(IBPIService $bpiService) 
    {
        $this->bpiService = $bpiService;   
    }

    public function bpiAuth(Request $request) {
        $response = $this->bpiService->bpiAuth($request->code);
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }

    public function getAccounts(Request $request) {
        $response = $this->bpiService->getAccounts($request->token);
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }

    public function fundTopUp(Request $request) {
        $data = [
            'accountNumberToken' => $request->accountNumberToken,
            'amount' => $request->amount,
            'remarks' => $request->remarks
        ];
        $response = $this->bpiService->fundTopUp($data, $request->token);
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }

    public function otp(Request $request) {
        $response = $this->bpiService->otp($request->all());
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }

    public function process(Request $request) {
        $response = $this->bpiService->process($request->all());
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }

    public function status(Request $request) {
        $response = $this->bpiService->status($request->all());
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }
}
