<?php

namespace App\Http\Controllers;

use App\Enums\UsernameTypes;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\Auth\IAuthService;
use App\Services\Encryption\IEncryptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $username = $this->getUsernameField($request);

        $token = $this->authService->login($username, $login, $ip);
        $response = [
            'access_token' => $token->plainTextToken,
            'created_at' => $token->accessToken->created_at,
            'expires_in' => config('sanctum.expiration')
        ];

        $encryptedResponse = $this->encryptionService->encrypt($response);
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Generates OTP for password recovery verification
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->forgotPassword($usernameField, $data[$usernameField]);
        return response()->json([],Response::HTTP_OK);
    }


    /**
     * Verifies OTPs
     *
     * @param VerifyOtpRequest $request
     * @return JsonResponse
     */
    public function verify(VerifyOtpRequest  $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->verify($usernameField, $data['code_type'], $data[$usernameField], $data['code']);
        return response()->json([
            $usernameField => $data[$usernameField],
            'status' => 'success'
        ], Response::HTTP_OK);
    }

    /**
     * Reset a user accounts password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->resetPassword($usernameField, $data[$usernameField], $data['password']);
        return response()->json(null, Response::HTTP_OK);
    }

    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }
}
