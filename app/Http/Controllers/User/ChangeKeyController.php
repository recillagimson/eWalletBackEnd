<?php

namespace App\Http\Controllers\User;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ValidateCurrentKeyRequest;
use App\Http\Requests\Auth\VerifyUserKeyRequest;
use App\Http\Requests\ValidateKeyRequest;
use App\Services\v2\Auth\IAuthService;
use App\Services\Auth\UserKey\IUserKeyService;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\RouteParamHelpers;
use App\Traits\UserHelpers;
use Illuminate\Http\JsonResponse;

class ChangeKeyController extends Controller
{
    use UserHelpers, RouteParamHelpers;

    private IUserKeyService $keyService;
    private IResponseService $responseService;
    private IAuthService $authService;

    public function __construct(IUserKeyService  $keyService, IAuthService $authService,
                                IResponseService $responseService)
    {
        $this->keyService = $keyService;
        $this->authService = $authService;
        $this->responseService = $responseService;
    }

    public function validateCurrentKey(ValidateCurrentKeyRequest $request): JsonResponse
    {
        $keyType = $request->route('keyType');
        $this->validateUserKeyTypes($keyType);

        $data = $request->validated();
        $userId = $request->user()->id;
        $keyField = $this->getKeyFieldFromUserKeyType($keyType);

        $this->keyService->validateUser($request->user(), $keyType, $data['current_' . $keyField]);
        return $this->responseService->successResponse([], SuccessMessages::passwordValidationPassed);
    }

    /**
     * Validates user input and generate OTP
     *
     * @param ValidateKeyRequest $request
     * @return JsonResponse
     */
    public function validateKey(ValidateKeyRequest $request): JsonResponse
    {
        $keyType = $request->route('keyType');
        $this->validateUserKeyTypes($keyType);

        $data = $request->validated();
        $userId = $request->user()->id;
        $keyField = $this->getKeyFieldFromUserKeyType($keyType);
        $this->keyService->validateKey($userId, $data['current_' . $keyField], $data['new_' . $keyField],
            $keyType, true);

        return $this->responseService->successResponse([], SuccessMessages::passwordValidationPassed);
    }

    /**
     * Verifies if the OTP is valid
     *
     * @param VerifyUserKeyRequest $request
     * @return JsonResponse
     */
    public function verifyKey(VerifyUserKeyRequest $request): JsonResponse
    {
        $keyType = $request->route('keyType');
        $this->validateUserKeyTypes($keyType);

        $data = $request->validated();
        $userId = $request->user()->id;
        $otpType = $this->getOtpTypeFromUserKeyType($keyType);
        $this->authService->verify($userId, $otpType, $data['code'], $request->user()->otp_enabled ?? true);

        return $this->responseService->successResponse([], SuccessMessages::passwordRecoveryVerificationSuccessful);
    }

    /**
     * Updates the password / pin of the authenticated user
     *
     * @param ValidateKeyRequest $request
     * @return JsonResponse
     */
    public function changeKey(ValidateKeyRequest $request): JsonResponse
    {
        $keyType = $request->route('keyType');
        $this->validateUserKeyTypes($keyType);

        $data = $request->validated();
        $userId = $request->user()->id;
        $keyField = $this->getKeyFieldFromUserKeyType($keyType);
        $otpType = $this->getOtpTypeFromUserKeyType($keyType);

        $this->keyService->changeKey($userId, $data['current_' . $keyField], $data['new_' . $keyField], $keyType,
            $otpType, true);

        return $this->responseService->successResponse([], SuccessMessages::passwordUpdateSuccessful);

    }


}
