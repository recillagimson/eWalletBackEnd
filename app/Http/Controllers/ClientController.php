<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\IAuthService;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\ClientLoginRequest;

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
        // dd(Hash::make('iZMsCTcJGyC1duTWR3=H&IG'));
        // dd($request->all());
        $clientLogin = $request->validated();
        $clientToken = $this->authService->clientLogin($clientLogin['client_id'], $clientLogin['client_secret']);

        $tokenResponse = [
            'access_token' => $clientToken->plainTextToken,
            'created_at' => $clientToken->accessToken->created_at,
            'expires_in' => config('sanctum.expiration')
        ];
        return response()->json($tokenResponse, Response::HTTP_OK);
    }
}
