<?php

namespace App\Http\Controllers;

use App\Enums\UsernameTypes;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\IAuthService;
use App\Services\Encryption\IEncryptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    private IAuthService $authService;
    private IEncryptionService $encryptionService;

    public function __construct(IAuthService $authService, IEncryptionService $encryptionService)
    {
        $this->authService = $authService;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Registers a user
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $newUser = $request->validated();
        $user = $this->authService->register($newUser);
        $encryptedResponse = $this->encryptionService->encrypt($user->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }


    /**
     * Authenticate a user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->validated();
        $ip = $request->ip();
        $username = $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;

        $token = $this->authService->login($username, $login, $ip);
        $response = [
            'access_token' => $token->plainTextToken,
            'created_at' => $token->accessToken->created_at,
            'expires_in' => config('sanctum.expiration')
        ];

        $encryptedResponse = $this->encryptionService->encrypt($response);
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }
}
