<?php
namespace App\Services\Auth;


use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\Notifications\INotificationService;
use Laravel\Sanctum\NewAccessToken;

/**
 * @property IUserAccountRepository $userAccounts
 *
 */
interface IAuthService
{
    /**
     * Attempts to authenticate the user with the
     * provided credentials when using a web client
     *
     * @param string $usernameField
     * @param array $creds
     * @param string $ip
     * @return array
     */
    public function login(string $usernameField, array $creds, string $ip): array;

    /**
     * Attempts to authenticate the user with the
     * provided credentials when using mobile apps.
     *
     * @param string $usernameField
     * @param array $creds
     * @return array
     */
    public function mobileLogin(string $usernameField, array $creds): array;

    /**
     * Authenticates Client Applications
     *
     * @param string $clientId
     * @param string $clientSecret
     * @return NewAccessToken
     */
    public function clientLogin(string $clientId, string $clientSecret): NewAccessToken;

    /**
     * Pin authentication for confirmation to
     * proceed in transactions
     *
     * @param string $userId
     * @param string $pinCode
     */
    public function confirmTransactions(string $userId, string $pinCode);

    /**
     * Verifies the validity of OTPs
     *
     *
     * @param string $userId
     * @param string $verificationType
     * @param string $otp
     */
    public function verify(string $userId, string $verificationType, string $otp);

    /**
     * Validate OTP and provides user token
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otp
     * @return void
     */
    public function verifyLogin(string $usernameField, string $username, string $otp);

    /**
     * Generates an OTP for mobile login
     *
     * @param string $usernameField
     * @param string $username
     */
    public function generateMobileLoginOTP(string $usernameField, string $username);

    /**
     * Send OTP
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otpType
     * @param INotificationService|null $notifService
     */
    public function sendOTP(string $usernameField, string $username, string $otpType, INotificationService $notifService = null);
}
