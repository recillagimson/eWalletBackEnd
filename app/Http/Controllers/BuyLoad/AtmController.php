<?php

namespace App\Http\Controllers\BuyLoad;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuyLoad\ATM\GenerateSignatureRequest;
use App\Http\Requests\BuyLoad\ATM\VerifySignatureRequest;
use App\Services\Utilities\PrepaidLoad\ATM\IAtmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\PrepaidLoad\ATMTopUpRequest;

class AtmController extends Controller
{
    private IAtmService $atmService;
    private IResponseService $responseService;

    public function __construct(IAtmService $atmService,
                                IResponseService $responseService)
    {
        $this->atmService = $atmService;
        $this->responseService = $responseService;
    }

    public function generate(GenerateSignatureRequest $request): JsonResponse
    {
        $data = $request->post();
        $signature = $this->atmService->generateSignature($data);

        $response = [
            'signature' => $signature
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function verify(VerifySignatureRequest $request): JsonResponse
    {
        $signature = $request->header('Signature');
        $data = $request->post();
        $this->atmService->verifySignature($data, $signature);

        $response = [
            'message' => SuccessMessages::success
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function showPrefixNetworkList() {
        $records = $this->atmService->showNetworkAndPrefix();

        return $this->responseService->successResponse($records, SuccessMessages::success);
    }

    public function showProductList() {
        $records = $this->atmService->showProductList();

        return $this->responseService->successResponse($records, SuccessMessages::success);
    }

    public function load(ATMTopUpRequest $atmrequest): JsonResponse {
        $details = $atmrequest->validated();

        $records = $this->atmService->atmload($details);

        return $this->responseService->successResponse($records, SuccessMessages::success);
    }
}
