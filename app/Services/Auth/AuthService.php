<?php

namespace App\Services\Auth;

use App\Enums\OtpTypes;
use App\Enums\TokenNames;
use App\Enums\UsernameTypes;
use App\Models\UserAccount;
use App\Repositories\Client\IClientRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserKeys\PasswordHistory\IPasswordHistoryRepository;
use App\Repositories\UserKeys\PinCodeHistory\IPinCodeHistoryRepository;
use App\Services\Transaction\ITransactionService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\UserHelpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\NewAccessToken;

class AuthService implements IAuthService
{
    use UserHelpers, WithAuthErrors;

    private int $maxLoginAttempts;
    private int $daysToResetAttempts;
    private int $remainingAgeToNotify;
    private int $maxPasswordAge;
    private int $tokenExpiration;

    public IUserAccountRepository $userAccounts;
    public IClientRepository $clients;
    public IPasswordHistoryRepository $passwordHistories;
    public IPinCodeHistoryRepository $pinCodeHistories;

    private INotificationService $notificationService;
    private IOtpService $otpService;
    private IEmailService $emailService;
    private ISmsService $smsService;
    private ITransactionService $transactionService;


    public function __construct(IUserAccountRepository $userAccts,
                                IPasswordHistoryRepository $passwordHistories,
                                IPinCodeHistoryRepository $pinCodeHistories,
                                IClientRepository $clients,
                                IEmailService $emailService,
                                ISmsService $smsService,
                                INotificationService $notificationService,
                                IOtpService $otpService,
                                ITransactionService $transactionService)
    {
        $this->maxLoginAttempts = config('auth.account_lockout_attempt');
        $this->daysToResetAttempts = config('auth.account_lockout_attempt_reset');
        $this->remainingAgeToNotify = config('auth.password_notify_expire');
        $this->maxPasswordAge = config('auth.password_max_age_np');
        $this->tokenExpiration = config('sanctum.expiration');

        $this->userAccounts = $userAccts;
        $this->clients = $clients;
        $this->passwordHistories = $passwordHistories;
        $this->pinCodeHistories = $pinCodeHistories;

        $this->otpService = $otpService;
        $this->notificationService = $notificationService;
        $this->emailService = $emailService;
        $this->smsService = $smsService;

        $this->transactionService = $transactionService;
    }

    public function login(string $usernameField, array $creds, string $ip): array
    {
        $user = $this->userAccounts->getByUsername($usernameField, $creds[$usernameField]);
        $this->validateInternalUsers($user);
        $this->validateUser($user);

        $this->tryLogin($user, $creds['password'], $user->password);

        $firstLogin = !$user->last_login;
        $this->updateLastLogin($user, $usernameField);


        //$this->transactionService->processUserPending($user);

        $user->deleteAllTokens();
        return $this->generateLoginToken($user, TokenNames::userWebToken, $firstLogin);
    }

    public function mobileLogin(string $usernameField, array $creds): array
    {
        $user = $this->userAccounts->getByUsername($usernameField, $creds[$usernameField]);
        $this->validateInternalUsers($user);

        $this->validateUser($user);
        $this->tryLogin($user, $creds['pin_code'], $user->pin_code);

        $firstLogin = !$user->last_login;
        $this->updateLastLogin($user, $usernameField);

        //$this->transactionService->processUserPending($user);

        $user->deleteAllTokens();
        return $this->generateLoginToken($user, TokenNames::userMobileToken, $firstLogin);
    }

    public function adminLogin(string $email, string $password): array
    {
        $user = $this->userAccounts->getByUsername(UsernameTypes::Email, $email);
        if (!$user) $this->loginFailed();
        if (!$user->is_admin) $this->loginFailed();

        $this->validateUser($user);
        $this->tryLogin($user, $password, $user->password);

        $firstLogin = !$user->last_login;
        $this->updateLastLogin($user, UsernameTypes::Email);

        $user->deleteAllTokens();
        return $this->generateLoginToken($user, TokenNames::userMobileToken, $firstLogin);
    }

    public function partnersLogin(string $mobileNumber, string $password)
    {
        $user = $this->userAccounts->getByUsername(UsernameTypes::MobileNumber, $mobileNumber);
        if (!$user) $this->loginFailed();
        if (!$user->is_onboarder && !$user->is_merchant) $this->loginFailed();

        $this->validateUser($user);
        $this->tryLogin($user, $password, $user->password);

        $this->generateMobileLoginOTP(UsernameTypes::MobileNumber, $mobileNumber);
    }

    public function partnersVerifyLogin(string $mobileNumber, string $otp): array
    {
        $user = $this->userAccounts->getByUsername(UsernameTypes::MobileNumber, $mobileNumber);
        if (!$user) $this->loginFailed();
        if (!$user->is_onboarder && !$user->is_merchant) $this->loginFailed();

        $this->validateUser($user);
        $this->verifyLogin(UsernameTypes::MobileNumber, $mobileNumber, $otp);

        $firstLogin = !$user->last_login;
        $this->updateLastLogin($user, UsernameTypes::MobileNumber);

        $user->deleteAllTokens();
        return $this->generateLoginToken($user, TokenNames::userMobileToken, $firstLogin);
    }

    public function clientLogin(string $clientId, string $clientSecret): NewAccessToken
    {
        $client = $this->clients->getClient($clientId);

        if (!$client || !Hash::check($clientSecret, $client->client_secret)) {
            $this->invalidCredentials();
        }

        return $client->createToken(TokenNames::clientToken);
    }

    public function confirmTransactions(string $userId, string $pinCode)
    {
        $user = $this->userAccounts->get($userId);
        if (!$user) $this->confirmationFailed();

        $pinCodeMatch = Hash::check($pinCode, $user->pin_code);
        if (!$pinCodeMatch) {
            $user->updateLockout($this->maxLoginAttempts);
            $this->confirmationFailed();
        }
    }

    public function verify(string $userId, string $verificationType, string $otp, bool $otpEnabled = true)
    {
        if (App::environment('local') || !$otpEnabled) {
            if ($otp === "1111") return;
            else $this->otpInvalid('Invalid OTP.');
        }


        $identifier = $verificationType . ':' . $userId;
        $otpValidity = $this->otpService->validate($identifier, $otp);
        if (!$otpValidity->status) $this->otpInvalid($otpValidity->message);
    }

    public function verifyLogin(string $usernameField, string $username, string $otp)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if (!$user) $this->accountDoesntExist();

        $this->verify($user->id, OtpTypes::login, $otp, $user->otp_enabled);
    }

    public function generateTransactionOTP(UserAccount $user, string $otpType, ?string $type)
    {
        $usernameField = $user->is_login_email ? UsernameTypes::Email : UsernameTypes::MobileNumber;
        $username = $user->is_login_email ? $user->email : $user->mobile_number;
        $notifService = $user->is_login_email ? $this->emailService : $this->smsService;

        $this->sendOTP($usernameField, $username, $otpType, $notifService);
    }

    public function generateMobileLoginOTP(string $usernameField, string $username)
    {
        $this->sendOTP($usernameField, $username, OtpTypes::login);
    }

    public function sendOTP(string $usernameField, string $username, string $otpType, INotificationService $notifService = null)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if (!$user) $this->accountDoesntExist();

        $recipientName = $user->profile ? ucwords($user->profile->first_name) : 'Squidee';
        $otp = $this->generateOTP($otpType, $user->id, $user->otp_enabled);

        Log::debug('Generated OTP For User: ', [
            'recipientName' => $recipientName,
            'userId' => $user->id,
            'otp' => $otp
        ]);

        if (App::environment('local') || !$user->otp_enabled) return;

        $notif = $notifService == null ? $this->notificationService : $notifService;

        if ($otpType === OtpTypes::registration)
            $notif->sendAccountVerification($username, $otp->token, $recipientName);
        elseif ($otpType === OtpTypes::login)
            $notif->sendLoginVerification($username, $otp->token, $recipientName);
        elseif ($otpType === OtpTypes::passwordRecovery || $otpType === OtpTypes::pinRecovery)
            $notif->sendPasswordVerification($username, $otp->token, $otpType, $recipientName);
        elseif ($otpType === OtpTypes::sendMoney)
            $notif->sendMoneyVerification($username, $otp->token, $recipientName);
        elseif ($otpType === OtpTypes::send2Bank)
            $notif->sendS2BVerification($username, $otp->token, $recipientName);
        elseif ($otpType === OtpTypes::updateProfile)
            $notif->updateProfileVerification($username, $otp->token, $recipientName);
        else
            $this->otpTypeInvalid();
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */

    private function generateLoginToken(UserAccount $user, string $tokenType, bool $firstLogin): array
    {
        $token = $user->createToken($tokenType);
        $latestPassword = $this->passwordHistories->getLatest($user->id);
        $latestPin = $this->pinCodeHistories->getLatest($user->id);
        $passwordAboutToExpire = $latestPassword ? $latestPassword->isAboutToExpire($this->remainingAgeToNotify, $this->maxPasswordAge) : false;
        $pinAboutToExpire = $latestPin ? $latestPin->isAboutToExpire($this->remainingAgeToNotify, $this->maxPasswordAge) : false;

        return [
            'user_id' => $user->id,
            'user_token' => [
                'access_token' => $token->plainTextToken,
                'created_at' => $token->accessToken->created_at,
                'expires_in' => $this->tokenExpiration
            ],
            'notify_password_expiration' => $passwordAboutToExpire,
            'password_age' => $latestPassword ? $latestPassword->password_age : 0,
            'notify_pin_expiration' => $pinAboutToExpire,
            'pin_age' => $latestPin ? $latestPin->pin_age : 0,
            'first_login' => $firstLogin,
        ];
    }

    public function generateOTP(string $otpType, string $userId, bool $otpEnabled = true): object
    {
        if (App::environment('local') || !$otpEnabled) {
            return (object)[
                'status' => true,
                'token' => "1111",
                'message' => "OTP generated",
            ];
        }

        $identifier = $otpType . ':' . $userId;
        $otp = $this->otpService->generate($identifier);
        if (!$otp->status) $this->otpInvalid($otp->message);

        return $otp;
    }

    private function validateInternalUsers(?UserAccount $user)
    {
        if (!$user) $this->loginFailed();
        if ($user->is_admin) $this->loginFailed();
        if ($user->is_onboarder) $this->loginFailed();
        if ($user->is_merchant) $this->loginFailed();
    }

    private function validateUser(UserAccount $user)
    {
        if (!$user->verified) $this->accountDoesntExist();
        if ($user->is_lockout) $this->accountLockedOut();

        $user->resetLoginAttempts($this->daysToResetAttempts);
    }

    private function tryLogin(UserAccount $user, string $key, string $hashedKey)
    {
        $passwordMatched = Hash::check($key, $hashedKey);
        if (!$passwordMatched) {
            $user->updateLockout($this->maxLoginAttempts);
            $this->loginFailed();
        }

        $user->resetLoginAttempts($this->daysToResetAttempts, true);
    }

    private function updateLastLogin(UserAccount $user, string $usernameField)
    {
        $user->last_login = Carbon::now();
        $user->is_login_email = $usernameField == UsernameTypes::Email;
        $user->save();
    }
}
