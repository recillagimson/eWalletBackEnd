<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OtpTypes;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPinOrPasswordRequest;
use App\Http\Requests\Auth\ResetPinRequest;
use App\Http\Requests\Auth\VerifyPinOrPasswordRequest;
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
     * @param ForgotPinOrPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPin(ForgotPinOrPasswordRequest $request): JsonResponse
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
     * @param VerifyPinOrPasswordRequest $request
     * @return JsonResponse
     */
    public function verifyPin(VerifyPinOrPasswordRequest $request): JsonResponse
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
