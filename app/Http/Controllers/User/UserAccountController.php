<?php

namespace App\Http\Controllers\User;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateEmailRequest;
use App\Http\Requests\User\UpdateMobileRequest;
use App\Services\UserAccount\IUserAccountService;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\UserHelpers;
use App\Services\UserAccount\IUserAccountService;
use App\Http\Resources\UserAccount\UserAccountCollection;
use Illuminate\Http\JsonResponse;

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

    public function index(): JsonResponse 
    {
        $records = $this->userAccountService->getAllPaginated();
        $records = new UserAccountCollection($records);

        return $this->responseService->successResponse($records->toArray($records) , SuccessMessages::success);
    }

    public function show(string $id): JsonResponse
    {
        $record = $this->userAccountService->findById($id);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function validateEmail(UpdateEmailRequest $request): JsonResponse
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

    public function validateMobile(UpdateMobileRequest $request): JsonResponse
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
