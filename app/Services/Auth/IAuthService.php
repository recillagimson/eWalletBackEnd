<?php
namespace App\Services\Auth;


use App\Repositories\UserAccount\IUserAccountRepository;
use Laravel\Sanctum\NewAccessToken;

/**
 * @property IUserAccountRepository $userAccounts
 *
 */
interface IAuthService {
    public function register(array $newUser);
    public function login(string $usernameField, array $creds, string $ip);
    public function clientLogin(string $clientId, string $clientSecret): NewAccessToken;
}
