<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Enums\UsernameTypes;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterPinCodeRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyAccountRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\UserAccount;
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
        $usernameField = $this->getUsernameField($request);
        $user = $this->authService->register($newUser, $usernameField);

        $encryptedData = $this->encryptionService->encrypt($user->toArray());
        $response = [
            'message' => SuccessMessages::accountRegistered,
            'data' => $encryptedData
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }


    /**
     * Validates the registration otp and verifies the account
     *
     * @param VerifyAccountRequest $request
     * @return JsonResponse
     */
    public function verifyAccount(VerifyAccountRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->verifyAccount($usernameField, $data[$usernameField], $data['code']);

        $response = [
            'message' => SuccessMessages::accountVerification,
            'data' => [$usernameField => $data[$usernameField]]
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Updates the pin code for the newly registered
     * account.
     *
     * @param RegisterPinCodeRequest $request
     * @return JsonResponse
     */
    public function registerPin(RegisterPinCodeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->registerPIN($usernameField, $data[$usernameField], $data['pin_code']);

        $response = [
            'message' => SuccessMessages::pinCodeUpdated,
            'data' => [$usernameField => $data[$usernameField]]
        ];
        return response()->json($response, Response::HTTP_OK);
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

    /**
     * Get the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUser(Request $request): JsonResponse
    {
        $user = $request->user();
        if(!$user instanceof UserAccount) return response()->json(null, Response::HTTP_UNAUTHORIZED);
        return response()->json($request->user(), Response::HTTP_OK);
    }

    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }
}
