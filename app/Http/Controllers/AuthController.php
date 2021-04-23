<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Enums\UsernameTypes;
use App\Http\Requests\Auth\ConfirmTransactionRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\MobileLoginRequest;
use App\Http\Requests\Auth\MobileLoginValidateRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\ValidateNewUserRequest;
use App\Http\Requests\Auth\VerifyAccountRequest;
use App\Http\Requests\Auth\VerifyLoginRequest;
use App\Models\UserAccount;
use App\Services\Auth\IAuthService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    private IAuthService $authService;
    private IResponseService $responseService;

    public function __construct(IAuthService $authService, IResponseService $responseService)
    {
        $this->authService = $authService;
        $this->responseService = $responseService;
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

        return $this->responseService->createdResponse($user->toArray(), SuccessMessages::accountRegistered);
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

        return $this->responseService->successResponse([
            $usernameField => $newUser[$usernameField]
        ], SuccessMessages::accountValidationPassed);
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

        return $this->responseService->successResponse($loginResponse, SuccessMessages::loginSuccessful);
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

        return $this->responseService->successResponse($loginResponse, SuccessMessages::loginSuccessful);
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

        return $this->responseService->successResponse([
            $usernameField => $login[$usernameField]
        ], SuccessMessages::loginValidationPassed);
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

        return $this->responseService->successResponse([$usernameField => $data[$usernameField]], SuccessMessages::accountVerification);
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

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::loginVerificationSuccessful);
    }

    /**
     * Authentication via pin code to confirm a specific
     * transaction
     *
     * @param ConfirmTransactionRequest $request
     * @return JsonResponse
     */
    public function confirmTransactions(ConfirmTransactionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $this->authService->confirmTransactions($user->id, $data['pin_code']);

        $response = [
            'mobile_number' => $user->mobile_number
        ];

        return $this->responseService->successResponse($response, SuccessMessages::confirmationSuccessful);
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

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::otpSent);
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
