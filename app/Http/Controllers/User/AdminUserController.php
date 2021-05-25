<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateAdminUserRequest;
use App\Http\Requests\User\DeleteAdminUserRequest;
use App\Http\Requests\User\GetAdminUserRequest;
use App\Http\Requests\User\SearchAdminUserByEmailRequest;
use App\Http\Requests\User\SearchAdminUserByNameRequest;
use App\Http\Requests\User\UpdateAdminUserRequest;
use App\Http\Resources\User\AdminUserResource;
use App\Services\UserAccount\IUserAccountService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    private IUserAccountService $userService;
    private IResponseService $responseService;

    public function __construct(IUserAccountService $userService, IResponseService $responseService)
    {
        $this->userService = $userService;
        $this->responseService = $responseService;
    }

    public function get(GetAdminUserRequest $request): JsonResponse
    {
        $users = $this->userService->getAdminUsers();
        $usersResource = json_decode(AdminUserResource::collection($users)->toJson());

        return $this->responseService->successResponse($usersResource);
    }

    public function getById(GetAdminUserRequest $request): JsonResponse
    {
        $userId = $request->route('id');
        $user = $this->userService->users->getAdminUser($userId);

        $userResource = new AdminUserResource($user);
        $response = json_decode($userResource->toJson(), true);

        return $this->responseService->successResponse($response);
    }

    public function getByEmail(SearchAdminUserByEmailRequest $request): JsonResponse
    {
        $data = $request->validated();
        $users = $this->userService->getAdminUsersByEmail($data['email']);
        $usersResource = json_decode(AdminUserResource::collection($users)->toJson());

        return $this->responseService->successResponse($usersResource);
    }

    public function getByName(SearchAdminUserByNameRequest $request): JsonResponse
    {
        $data = $request->validated();
        $users = $this->userService->getAdminUsersByName($data['last_name'], $data['first_name']);
        $usersResource = json_decode(AdminUserResource::collection($users)->toJson());

        return $this->responseService->successResponse($usersResource);
    }

    public function create(CreateAdminUserRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $data = $request->validated();

        $user = $this->userService->createAdminUser($data, $userId);
        $userResource = new AdminUserResource($user);
        $response = json_decode($userResource->toJson(), true);

        return $this->responseService->createdResponse($response);
    }

    public function update(UpdateAdminUserRequest $request): JsonResponse
    {
        $userUpdateId = $request->user()->id;
        $userId = $request->route('id');
        $data = $request->validated();

        $user = $this->userService->updateAdminUser($userId, $data, $userUpdateId);
        $userResource = new AdminUserResource($user);
        $response = json_decode($userResource->toJson(), true);

        return $this->responseService->successResponse($response);
    }

    public function delete(DeleteAdminUserRequest $request): JsonResponse
    {
        $userId = $request->route('id');
        $this->userService->deleteAdminUser($userId);
        return $this->responseService->noContentResponse();
    }
}
