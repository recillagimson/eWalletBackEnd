<?php

namespace App\Http\Controllers\User;

use App\Traits\UserHelpers;
use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateEmailRequest;
use App\Http\Requests\User\UpdateMobileRequest;
use App\Services\UserAccount\IUserAccountService;
use App\Http\Requests\UserRole\SetUserRoleRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Resources\UserAccount\UserAccountCollection;
use App\Repositories\UserUtilities\UserRole\IUserRoleRepository;

class UserAccountController extends Controller
{
    use UserHelpers;

    private IUserAccountService $userAccountService;
    private IResponseService $responseService;
    private IUserRoleRepository $userRoleRepository;

    public function __construct(IUserAccountService $userAccountService, IResponseService $responseService, IUserRoleRepository $userRoleRepository)
    {
        $this->userAccountService = $userAccountService;
        $this->responseService = $responseService;
        $this->userRoleRepository = $userRoleRepository;
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

    public function setAccountRole(SetUserRoleRequest $request) {
        $records =  $this->userRoleRepository->setUserRoles($request->all());
        return $this->responseService->successResponse($records, SuccessMessages::success);
    }
}
