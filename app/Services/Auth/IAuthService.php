<?php
namespace App\Services\Auth;


use App\Repositories\UserAccount\IUserAccountRepository;
use Laravel\Sanctum\NewAccessToken;

/**
 * @property IUserAccountRepository $userAccounts
 *
 */
interface IAuthService {
    public function register(array $newUser, string $usernameField);
    public function verifyAccount(string $usernameField, string $username, string $otp);
    public function registerPIN(string $usernameField, string $username, string $pinCode);
    public function login(string $usernameField, array $creds, string $ip);
    public function forgotPassword(string $usernameField, string $username);
    public function resetPassword(string $usernameField, string $username, string $password);
    public function clientLogin(string $clientId, string $clientSecret): NewAccessToken;
}
