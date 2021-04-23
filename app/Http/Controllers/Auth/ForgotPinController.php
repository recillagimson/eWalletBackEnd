<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OtpTypes;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotKeyRequest;
use App\Http\Requests\Auth\ResetPinRequest;
use App\Http\Requests\Auth\VerifyKeyRequest;
use App\Services\Auth\IAuthService;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\UserHelpers;
use Illuminate\Http\JsonResponse;

class ForgotPinController extends Controller
{
    use UserHelpers;

    private IAuthService $authService;
    private IResponseService $responseService;

    public function __construct(IAuthService $authService,
                                IResponseService $responseService)
    {
        $this->authService = $authService;
        $this->responseService = $responseService;
    }

    /**
     * Generates OTP for pin recovery verification
     *
     * @param ForgotKeyRequest $request
     * @return JsonResponse
     */
    public function forgotPin(ForgotKeyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->forgotPinOrPassword($usernameField, $data[$usernameField], OtpTypes::pinRecovery);

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::passwordRecoveryRequestSuccessful);
    }

    /**
     * Verifies and validates otp for pin recovery
     *s
     * @param VerifyKeyRequest $request
     * @return JsonResponse
     */
    public function verifyPin(VerifyKeyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->verifyPinOrPassword($usernameField, $data[$usernameField], $data['code'], OtpTypes::pinRecovery);

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::passwordRecoveryVerificationSuccessful);
    }

    /**
     * Reset a user accounts pin code
     *
     * @param ResetPinRequest $request
     * @return JsonResponse
     */
    public function resetPin(ResetPinRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $this->authService->resetPinOrPassword($usernameField, $data[$usernameField], $data['pin_code'], OtpTypes::pinRecovery);

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::passwordUpdateSuccessful);
    }
}
