<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Auth\IUserService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    private IUserService $userService;
    private IResponseService $responseService;

    public function __construct(IUserService $userService, IResponseService $responseService)
    {
        $this->userService = $userService;
        $this->responseService = $responseService;
    }

    public function getBalanceInfo(string $userId): JsonResponse
    {
        $response = $this->userService->getBalanceInfo($userId);
        return $this->responseService->createdResponse($response);
    }
}
