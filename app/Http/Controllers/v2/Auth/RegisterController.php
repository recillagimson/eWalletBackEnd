<?php

namespace App\Http\Controllers\v2\Auth;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\v2\RegisterUserRequest;
use App\Http\Requests\Auth\ValidateNewUserRequest;
use App\Http\Requests\Auth\VerifyAccountRequest;
use App\Http\Requests\Auth\ValidatePinRequest;
use App\Services\Auth\Registration\IRegistrationService;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\UserHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    use UserHelpers;

    private IRegistrationService $registrationService;
    private IResponseService $responseService;

    public function __construct(IRegistrationService $registrationService,
                                IResponseService $responseService)
    {
        $this->registrationService = $registrationService;
        $this->responseService = $responseService;
    }

    // /**
    //  * Registers a user
    //  *
    //  * @param RegisterUserRequest $request
    //  * @return JsonResponse
    //  */
    // public function register(RegisterUserRequest $request): JsonResponse
    // {
    //     $newUser = $request->validated();
    //     $usernameField = $this->getUsernameField($request);
    //     $user = $this->registrationService->register($newUser, $usernameField);

    //     return $this->responseService->createdResponse($user->toArray(), SuccessMessages::accountRegistered);
    // }

    // /**
    //  * Validates User Registration Inputs
    //  *
    //  * @param ValidateNewUserRequest $request
    //  * @return JsonResponse
    //  * @throws ValidationException
    //  */
    // public function registerPin(ValidatePinRequest $request): JsonResponse
    // {
    //     $data = $request->validated();
    //     $usernameField = $this->getUsernameField($request);
    //     $token = $this->registrationService->registerPin($data, $usernameField);

    //     return $this->responseService->createdResponse($token, SuccessMessages::pinValidationPassed);
    // }

    // /**
    //  * Validates User Registration Inputs
    //  *
    //  * @param ValidateNewUserRequest $request
    //  * @return JsonResponse
    //  * @throws ValidationException
    //  */
    // public function registerValidate(ValidateNewUserRequest $request): JsonResponse
    // {
    //     $newUser = $request->validated();
    //     $usernameField = $this->getUsernameField($request);
    //     $this->registrationService->validateAccount($usernameField, $newUser[$usernameField]);

    //     return $this->responseService->successResponse([
    //         $usernameField => $newUser[$usernameField]
    //     ], SuccessMessages::accountValidationPassed);
    // }

    // /**
    //  * Validates the registration otp and verifies the account
    //  *
    //  * @param VerifyAccountRequest $request
    //  * @return JsonResponse
    //  */
    // public function verifyAccount(VerifyAccountRequest $request): JsonResponse
    // {
    //     $data = $request->validated();
    //     $usernameField = $this->getUsernameField($request);
    //     $this->registrationService->verifyAccount($usernameField, $data[$usernameField], $data['code']);

    //     return $this->responseService->successResponse([$usernameField => $data[$usernameField]], SuccessMessages::accountVerification);
    // }
}
