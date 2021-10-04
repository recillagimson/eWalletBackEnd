<?php

namespace App\Http\Controllers\UBP;

use App\Http\Controllers\Controller;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;

class OAuthController extends Controller
{
    private IUBPService $ubpService;
    private IResponseService $responseService;

    public function __construct(IUBPService $ubpService, IResponseService $responseService)
    {
        $this->ubpService = $ubpService;
        $this->responseService = $responseService;
    }

    public function generateAuthorizeUrl(): JsonResponse
    {
        $authUrl = $this->ubpService->generateAuthorizeUrl();
        return $this->responseService->successResponse(['authorizeUrl' => $authUrl]);
    }
}
