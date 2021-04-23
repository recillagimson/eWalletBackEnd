<?php

namespace App\Http\Controllers\Auth;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPinOrPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyPinOrPasswordRequest;
use App\Services\Auth\IAuthService;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\UserHelpers;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    use UserHelpers;

    private IAuthService $authService;
    private IResponseService $responseService;

    public function __construct(IAuthService $authService, IResponseService $responseService)
    {
        $this->authService = $authService;
        $this->responseService = $responseService;
    }

    /**
     * Generates OTP for password recovery verification
     *
     * @param ForgotPinOrPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPinOrPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->forgotPinOrPassword($usernameField, $data[$usernameField]);

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::passwordRecoveryRequestSuccessful);
    }

    /**
     * Verifies and validates otp for password recovery
     *
     * @param VerifyPinOrPasswordRequest $request
     * @return JsonResponse
     */
    public function verifyPassword(VerifyPinOrPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->verifyPinOrPassword($usernameField, $data[$usernameField], $data['code']);

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::passwordRecoveryVerificationSuccessful);
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
        $this->authService->resetPinOrPassword($usernameField, $data[$usernameField], $data['password']);

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::passwordUpdateSuccessful);
    }

}
