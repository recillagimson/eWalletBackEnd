<?php

namespace App\Http\Controllers\User;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateEmailRequest;
use App\Http\Requests\User\UpdateMobileRequest;
use App\Http\Requests\UserRole\SetUserRoleRequest;
use App\Http\Resources\UserAccount\UserAccountCollection;
use App\Http\Resources\UserAccount\UserAccountListCollection;
use App\Repositories\UserUtilities\UserRole\IUserRoleRepository;
use App\Services\UserAccount\IUserAccountService;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\UserHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function index(Request $request): JsonResponse
    {
        $records = $this->userAccountService->getAllPaginated($request);
        $records = new UserAccountListCollection($records);

        return $this->responseService->successResponse($records->toArray($request), SuccessMessages::success);
    }

    public function show(Request $request): JsonResponse
    {
        $id = $request->route('id');
        $record = $this->userAccountService->findById($id);
        $record = new UserAccountCollection($record);

        return $this->responseService->successResponse($record->toArray($request), SuccessMessages::success);
    }

    public function validateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $userId = $request->user()->id;
        $emailField = $this->getEmailField($request);

        $this->userAccountService->validateEmail($userId, $fillRequest[$emailField]);

        return $this->responseService->successResponse([
            $emailField => $fillRequest[$emailField]
        ], SuccessMessages::validateUpdateEmail);
    }

    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $emailField = $this->getEmailField($request);
        $postback = $this->userAccountService->updateEmail($fillRequest[$emailField], $request->user());

        return $this->responseService->successResponse($postback, SuccessMessages::updateEmailSuccessful);
    }

    public function validateMobile(UpdateMobileRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $userId = $request->user()->id;
        $mobileField = $this->getMobileField($request);

        $this->userAccountService->validateMobile($userId, $fillRequest[$mobileField]);

        return $this->responseService->successResponse([
            $mobileField => $fillRequest[$mobileField]
        ], SuccessMessages::validateUpdateMobile);
    }

    public function updateMobile(UpdateMobileRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $userId = $request->user()->id;
        $mobileField = $this->getMobileField($request);
        $postback = $this->userAccountService->updateMobile($userId, $fillRequest[$mobileField], $request->user());

        return $this->responseService->successResponse($postback, SuccessMessages::updateMobileSuccessful);
    }

    public function setAccountRole(SetUserRoleRequest $request): JsonResponse
    {
        $records = $this->userRoleRepository->setUserRoles($request->all());
        return $this->responseService->successResponse($records, SuccessMessages::success);
    }


}
