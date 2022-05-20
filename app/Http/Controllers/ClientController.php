<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ClientLoginRequest;
use App\Services\v2\Auth\IAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    private IAuthService $authService;

    public function __construct(IAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Client Apps Authentication Endpoint
     *
     *
     * @param ClientLoginRequest $request
     * @return JsonResponse
     */
    public function getToken(ClientLoginRequest $request): JsonResponse
    {
        $clientLogin = $request->validated();

        // for removal android temporary

        if ($clientLogin['client_id'] == 'gio-client') {
    
            $clientToken = $this->authService->clientLogin($clientLogin['client_id'], 'HMGIfmLD=#|_%7hCfrFl^i?');

        } else {

            // original code 
            $clientToken = $this->authService->clientLogin($clientLogin['client_id'], $clientLogin['client_secret']);

        }

    
        $tokenResponse = [
            'access_token' => $clientToken->plainTextToken,
            'created_at' => $clientToken->accessToken->created_at,
            'expires_in' => config('sanctum.expiration')
        ];
        return response()->json($tokenResponse, Response::HTTP_OK);
    }

}
