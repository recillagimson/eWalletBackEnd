<?php

namespace App\Http\Controllers\Auth;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotKeyRequest;
use App\Http\Requests\Auth\ResetKeyRequest;
use App\Http\Requests\Auth\VerifyKeyRequest;
use App\Services\Auth\UserKey\IUserKeyService;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\RouteParamHelpers;
use App\Traits\UserHelpers;
use Illuminate\Http\JsonResponse;

class ForgotKeyController extends Controller
{
    use UserHelpers, RouteParamHelpers;

    private IUserKeyService $userKeyService;
    private IResponseService $responseService;

    public function __construct(IUserKeyService $userKeyService, IResponseService $responseService)
    {
        $this->userKeyService = $userKeyService;
        $this->responseService = $responseService;
    }

    /**
     * Generates OTP for password recovery verification
     *
     * @param ForgotKeyRequest $request
     * @return JsonResponse
     */
    public function forgotKey(ForgotKeyRequest $request): JsonResponse
    {
        $keyType = $request->route('keyType');
        $this->validateUserKeyTypes($keyType);

        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $otpType = $this->getOtpTypeFromUserKeyType($keyType);
        $this->userKeyService->forgotKey($usernameField, $data[$usernameField], $otpType);

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::passwordRecoveryRequestSuccessful);
    }

    /**
     * Verifies and validates otp for password recovery
     *
     * @param VerifyKeyRequest $request
     * @return JsonResponse
     */
    public function verifyKey(VerifyKeyRequest $request): JsonResponse
    {
        $keyType = $request->route('keyType');
        $this->validateUserKeyTypes($keyType);

        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $otpType = $this->getOtpTypeFromUserKeyType($keyType);
        $this->userKeyService->verifyKey($usernameField, $data[$usernameField], $data['code'], $otpType);

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::passwordRecoveryVerificationSuccessful);
    }

    /**
     * Reset a user accounts password
     *
     * @param ResetKeyRequest $request
     * @return JsonResponse
     */
    public function resetKey(ResetKeyRequest $request): JsonResponse
    {
        $keyType = $request->route('keyType');
        $this->validateUserKeyTypes($keyType);

        $data = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $keyField = $this->getKeyFieldFromUserKeyType($keyType);
        $otpType = $this->getOtpTypeFromUserKeyType($keyType);

        $this->userKeyService->resetKey($usernameField, $data[$usernameField], $data[$keyField], $keyType, $otpType);

        return $this->responseService->successResponse([
            $usernameField => $data[$usernameField]
        ], SuccessMessages::passwordUpdateSuccessful);
    }

}
