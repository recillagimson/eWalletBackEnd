<?php

namespace App\Http\Controllers\BuyLoad;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuyLoad\ATM\GenerateSignatureRequest;
use App\Http\Requests\BuyLoad\ATM\VerifySignatureRequest;
use App\Services\Utilities\PrepaidLoad\ATM\IAtmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AtmController extends Controller
{
    private IAtmService $atmService;

    public function __construct(IAtmService $atmService)
    {
        $this->atmService = $atmService;
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
}
