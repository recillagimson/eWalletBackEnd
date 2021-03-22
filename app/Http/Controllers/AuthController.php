<?php

namespace App\Http\Controllers;

use App\Enums\UsernameTypes;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\IAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    private IAuthService $auth;

    public function __construct(IAuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Registers a user
     *
     * @param RegisterUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $newUser = $request->validated();
        $user = $this->auth->register($newUser);

        return response()->json($user, Response::HTTP_CREATED);
    }


    /**
     * Authenticate a user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $login = $request->validated();
        $ip = $request->ip();
        $username = $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;

        $token = $this->auth->login($username, $login, $ip);

        $response = [
            'access_token' => $token->plainTextToken,
            'created_at' => $token->accessToken->created_at
        ];
        return response()->json($response, Response::HTTP_OK);
    }

}
