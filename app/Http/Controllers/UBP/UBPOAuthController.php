<?php

namespace App\Http\Controllers\UBP;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\UBP\UbpLinkAccountRequest;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\UBP\IUbpAccountService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UBPOAuthController extends Controller
{
    private IUBPService $ubpService;
    private IResponseService $responseService;
    private IUbpAccountService $ubpAccountService;

    public function __construct(IUBPService        $ubpService,
                                IUbpAccountService $ubpAccountService,
                                IResponseService   $responseService)
    {
        $this->ubpService = $ubpService;
        $this->responseService = $responseService;
        $this->ubpAccountService = $ubpAccountService;
    }

    public function checkAccountLink(Request $request): JsonResponse
    {
        $this->ubpAccountService->checkAccountLink($request->user()->id);
        return $this->responseService->successResponse(null, SuccessMessages::accountLinkCheckSuccess);
    }

    public function generateAuthorizeUrl(): JsonResponse
    {
        $authUrl = $this->ubpService->generateAuthorizeUrl();
        return $this->responseService->successResponse(['authorizeUrl' => $authUrl]);
    }

    public function linkAccount(UbpLinkAccountRequest $request): JsonResponse
    {
        $data = $request->validated();
        $token = $this->ubpService->generateAccountToken($request->user()->id, $data['code']);

        $response = [
            'consented_on' => $token['consented_on'],
            'expires_in' => $token['expires_in'],
        ];

        return $this->responseService->successResponse($response, SuccessMessages::accountLinkingSuccess);
    }
}
