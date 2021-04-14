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

    public function login(string $usernameField, array $creds, string $ip): array;
    public function clientLogin(string $clientId, string $clientSecret): NewAccessToken;
    public function mobileLogin(string $usernameField, array $creds): array;

    public function forgotPassword(string $usernameField, string $username);
    public function resetPassword(string $usernameField, string $username, string $password);

    public function verifyAccount(string $usernameField, string $username, string $otp);
    public function verifyLogin(string $usernameField, string $username, string $otp);
    public function verifyPassword(string $usernameField, string $username, string $otp);

    public function generateMobileLoginOTP(string $usernameField, string $username);
}
