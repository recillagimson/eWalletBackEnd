<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateEmailRequest;
use App\Http\Requests\User\UpdateMobileRequest;
use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\UserHelpers;
use App\Services\UserAccount\IUserAccountService;

class UserAccountController extends Controller
{
    use UserHelpers; 

    private IUserAccountService $userAccountService;
    private IResponseService $responseService;

    public function __construct(IUserAccountService $userAccountService, IResponseService $responseService)
    {
        $this->userAccountService = $userAccountService;
        $this->responseService = $responseService;
    }

    public function validateEmail(UpdateEmailRequest $request)
    {
        $fillRequest = $request->validated();
        $emailField = $this->getEmailField($request);
        $review = $this->userAccountService->validateEmail($emailField, $fillRequest[$emailField]);

        return $this->responseService->successResponse([
            $emailField => $fillRequest[$emailField]
        ], SuccessMessages::validateUpdateEmail);
    }

    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $emailField = $this->getEmailField($request);
        $postback = $this->userAccountService->updateEmail($emailField, $fillRequest[$emailField], $request->user());

        return $this->responseService->successResponse($postback, SuccessMessages::updateEmailSuccessful);
    }

    public function validateMobile(UpdateMobileRequest $request)
    {
        $fillRequest = $request->validated();
        $mobileField = $this->getMobileField($request);
        $review = $this->userAccountService->validateMobile($mobileField, $fillRequest[$mobileField]);

        return $this->responseService->successResponse([
            $mobileField => $fillRequest[$mobileField]
        ], SuccessMessages::validateUpdateMobile);
    }

    public function updateMobile(UpdateMobileRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $mobileField = $this->getMobileField($request);
        $postback = $this->userAccountService->updateMobile($mobileField, $fillRequest[$mobileField], $request->user());

        return $this->responseService->successResponse($postback, SuccessMessages::updateMobileSuccessful);
    }
}
