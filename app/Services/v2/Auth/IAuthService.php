<?php
namespace App\Services\v2\Auth;


use App\Models\UserAccount;
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
     * Authenticates admin users
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function adminLogin(string $email, string $password): array;

    /**
     * Authenticates onboarders with otp
     *
     * @param string $mobileNumber
     * @param string $password
     * @return mixed
     */
    public function partnersLogin(string $mobileNumber, string $password);

    /**
     * Verifies the OTP for onboarders login
     *
     * @param string $mobileNumber
     * @param string $otp
     * @return array
     */
    public function partnersVerifyLogin(string $mobileNumber, string $otp): array;

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
     * @param bool $otpEnabled
     */
    public function verify(string $userId, string $verificationType, string $otp, bool $otpEnabled = true);

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
     * Generate otp for the authenticated user
     *
     * @param UserAccount $user
     * @param string $otpType
     * @param string|null $type
     * @return mixed
     */
    public function generateTransactionOTP(UserAccount $user, string $otpType, ?string $type);

    /**
     * Generates an otp token
     *
     * @param string $otpType
     * @param string $userId
     * @param bool $otpEnabled
     * @return object
     */
    public function generateOTP(string $otpType, string $userId, bool $otpEnabled = true): object;

    /**
     * Send OTP
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otpType
     * @param INotificationService|null $notifService
     */
    public function sendOTP(string $usernameField, string $username, string $otpType, INotificationService $notifService = null);

    /**
     * Operation confirmation via password for admin module
     *
     * @param string $userId
     * @param string $password
     * @return mixed
     */
    public function passwordConfirmation(string $userId, string $password);
}
