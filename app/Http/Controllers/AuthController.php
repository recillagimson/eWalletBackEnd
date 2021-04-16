<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Enums\UsernameTypes;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\MobileLoginRequest;
use App\Http\Requests\Auth\MobileLoginValidateRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ValidateNewUserRequest;
use App\Http\Requests\Auth\VerifyAccountRequest;
use App\Http\Requests\Auth\VerifyLoginRequest;
use App\Http\Requests\Auth\VerifyPasswordRequest;
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

        $response = [
            'message' => SuccessMessages::accountRegistered,
            'data' => $this->encryptionService->encrypt($user->toArray())
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }

    /**
     * Validates User Registration Inputs
     *
     * @param ValidateNewUserRequest $request
     * @return JsonResponse
     */
    public function registerValidate(ValidateNewUserRequest $request): JsonResponse
    {
        $newUser = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->checkAccount($usernameField, $newUser[$usernameField]);

        $response = [
            'message' => SuccessMessages::accountValidationPassed,
            'data' => $this->encryptionService->encrypt([
                $usernameField => $newUser[$usernameField]
            ])
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Authenticates a web client user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->validated();
        $ip = $request->ip();
        $usernameField = $this->getUsernameField($request);
        $loginResponse = $this->authService->login($usernameField, $login, $ip);

        $response = [
            'message' => SuccessMessages::loginSuccessful,
            'data' => $this->encryptionService->encrypt($loginResponse)
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Authenticates a mobile app user
     *
     * @param MobileLoginRequest $request
     * @return JsonResponse
     */
    public function mobileLogin(MobileLoginRequest $request): JsonResponse
    {
        $login = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $loginResponse = $this->authService->mobileLogin($usernameField, $login);

        $response = [
            'message' => SuccessMessages::loginSuccessful,
            'data' => $this->encryptionService->encrypt($loginResponse)
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Validate mobile login
     *
     * @param MobileLoginValidateRequest $request
     * @return JsonResponse
     */
    public function mobileLoginValidate(MobileLoginValidateRequest $request): JsonResponse
    {
        $login = $request->validated();
        $usernameField = $this->getUsernameField($request);

        $this->authService->generateMobileLoginOTP($usernameField, $login[$usernameField]);

        $response = [
            'messsage' => SuccessMessages::loginValidationPassed,
            'data' => $this->encryptionService->encrypt([
                $usernameField => $login[$usernameField]
            ])
        ];

        return response()->json($response, Response::HTTP_OK);
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

        $response = [
            'message' => SuccessMessages::passwordRecoveryRequestSuccessful,
            'data' => $this->encryptionService->encrypt([
                $usernameField => $data[$usernameField]
            ])
        ];
        return response()->json($response, Response::HTTP_OK);
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

        $response = [
            'message' => SuccessMessages::passwordUpdateSuccessful,
            'data' => $this->encryptionService->encrypt([
                $usernameField => $data[$usernameField]
            ])
        ];

        return response()->json($response, Response::HTTP_OK);
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
            'data' => $this->encryptionService->encrypt([$usernameField => $data[$usernameField]])
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Verify and validate login otp and generates token
     *
     * @param VerifyLoginRequest $request
     * @return JsonResponse
     */
    public function verifyMobileLogin(VerifyLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->verifyLogin($usernameField, $data[$usernameField], $data['code']);

        $response = [
            'message' => SuccessMessages::loginVerificationSuccessful,
            'data' => $this->encryptionService->encrypt([
                $usernameField => $data[$usernameField]
            ])
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Verifies and validates otp for password recovery
     *
     * @param VerifyPasswordRequest $request
     * @return JsonResponse
     */
    public function verifyPassword(VerifyPasswordRequest  $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->verifyPassword($usernameField, $data[$usernameField], $data['code']);

        $response = [
            'message' => SuccessMessages::passwordRecoveryVerificationSuccessful,
            'data' => $this->encryptionService->encrypt([
                $usernameField => $data[$usernameField]
            ])
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Generic method for resending otp
     *
     * @param ResendOtpRequest $request
     * @return JsonResponse
     */
    public function resendOTP(ResendOtpRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->sendOTP($usernameField, $data[$usernameField], $data['otp_type']);

        $response = [
            'message' => SuccessMessages::otpSent,
            'data' => $this->encryptionService->encrypt([
                $usernameField => $data[$usernameField]
            ])
        ];

        return response()->json($response, Response::HTTP_OK);
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
        if (!$user instanceof UserAccount) return response()->json(null, Response::HTTP_UNAUTHORIZED);
        return response()->json($request->user(), Response::HTTP_OK);
    }

    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }
}
