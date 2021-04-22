<?php
namespace App\Services\Auth;


use App\Enums\OtpTypes;
use App\Repositories\UserAccount\IUserAccountRepository;
use Laravel\Sanctum\NewAccessToken;

/**
 * @property IUserAccountRepository $userAccounts
 *
 */
interface IAuthService
{
    public function register(array $newUser, string $usernameField);

    public function login(string $usernameField, array $creds, string $ip): array;

    public function clientLogin(string $clientId, string $clientSecret): NewAccessToken;

    public function mobileLogin(string $usernameField, array $creds): array;

    public function confirmTransactions(string $userId, string $pinCode);

    public function forgotPinOrPassword(string $usernameField, string $username, string $otpType = OtpTypes::passwordRecovery);

    public function resetPinOrPassword(string $usernameField, string $username, string $pinOrPassword,
                                       string $otpType = OtpTypes::passwordRecovery);

    public function verifyAccount(string $usernameField, string $username, string $otp);

    public function verifyLogin(string $usernameField, string $username, string $otp);

    public function verifyPinorPassword(string $usernameField, string $username, string $otp,
                                        string $otpType = OtpTypes::passwordRecovery);

    public function generateMobileLoginOTP(string $usernameField, string $username);

    public function sendOTP(string $usernameField, string $username, string $otpType);

    public function checkAccount(string $usernameField, string $username);
}
