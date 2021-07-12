<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Enums\UsernameTypes;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\Auth\ConfirmTransactionRequest;
use App\Http\Requests\Auth\GenerateTransOtpRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\MobileLoginRequest;
use App\Http\Requests\Auth\MobileLoginValidateRequest;
use App\Http\Requests\Auth\PartnersLoginRequest;
use App\Http\Requests\Auth\PartnersVerifyLoginRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyLoginRequest;
use App\Http\Requests\Auth\VerifyTransOtpRequest;
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
     * Authenticates admin users
     *
     * @param AdminLoginRequest $request
     * @return JsonResponse
     */
    public function adminLogin(AdminLoginRequest $request): JsonResponse
    {
        $login = $request->validated();
        $loginResponse = $this->authService->adminLogin($login['email'], $login['password']);

        return $this->responseService->successResponse($loginResponse, SuccessMessages::loginSuccessful);
    }

    /**
     * Authenticates onboarders
     *
     * @param PartnersLoginRequest $request
     * @return JsonResponse
     */
    public function partnersLogin(PartnersLoginRequest $request): JsonResponse
    {
        $login = $request->validated();
        $this->authService->partnersLogin($login['mobile_number'], $login['password']);

        return $this->responseService->successResponse(['message' => 'Login successful but requires OTP verification.'], SuccessMessages::loginSuccessful);
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
     * Verify and validate onboarders login otp and generates token
     *
     * @param PartnersVerifyLoginRequest $request
     * @return JsonResponse
     */
    public function verifyPartnersLogin(PartnersVerifyLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $verificationResponse = $this->authService->partnersVerifyLogin($data['mobile_number'], $data['code']);

        return $this->responseService->successResponse($verificationResponse, SuccessMessages::loginVerificationSuccessful);
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
     * Generate OTP for transactions.
     *
     * @param GenerateTransOtpRequest $request
     * @return JsonResponse
     */
    public function generateTransactionOTP(GenerateTransOtpRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $this->authService->generateTransactionOTP($user, $data['otp_type']);

        return $this->responseService->successResponse([], SuccessMessages::otpSent);
    }

    /**
     * Verifies validity of transaction otps.
     *
     * @param VerifyTransOtpRequest $request
     * @return JsonResponse
     */
    public function verifyTransactionOtp(VerifyTransOtpRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $this->authService->verify($user->id, $data['otp_type'], $data['code'], $user->otp_enabled);
        return $this->responseService->successResponse([], SuccessMessages::otpVerificationSuccessful);
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
        $user = $request->user();
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
