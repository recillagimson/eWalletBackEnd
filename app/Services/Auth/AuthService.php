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
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use Carbon\Carbon;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\UserHelpers;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;

class AuthService implements IAuthService
{
    use UserHelpers, WithAuthErrors;

    private int $maxLoginAttempts;
    private int $daysToResetAttempts;
    private int $remainingAgeToNotify;
    private int $maxPasswordAge;
    private int $tokenExpiration;

    private ILogHistoryService $loghistoryService;


    public IUserAccountRepository $userAccounts;
    public IClientRepository $clients;
    public IPasswordHistoryRepository $passwordHistories;
    public IPinCodeHistoryRepository $pinCodeHistories;

    private INotificationService $notificationService;
    private IOtpService $otpService;
    private IEmailService $emailService;
    private ISmsService $smsService;


    public function __construct(IUserAccountRepository $userAccts,
                                IPasswordHistoryRepository $passwordHistories,
                                IPinCodeHistoryRepository $pinCodeHistories,
                                IClientRepository $clients,
                                IEmailService $emailService,
                                ISmsService $smsService,
                                INotificationService $notificationService,
                                IOtpService $otpService,
                                ILogHistoryService $loghistoryService)
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
        $this->loghistoryService = $loghistoryService;
    }

    public function login(string $usernameField, array $creds, string $ip): array
    {
        $user = $this->userAccounts->getByUsername($usernameField, $creds[$usernameField]);
        if (!$user) $this->loginFailed();
        $this->validateUser($user);
        $this->tryLogin($user, $creds['password'], $user->password);
        $user->deleteAllTokens();

        //log history
        $user_account_id = $user->id;
        $squidpay_module = 'Web Login';
        $namespace = 'App\Services\Auth';
        $transaction_date = Carbon::now();
        $remarks = '';
        $operation = 'Login via web browser';
        $reference_number = '';
        $this->loghistoryService->logUserHistory($user_account_id, $reference_number, $squidpay_module, $namespace, $transaction_date, $remarks, $operation);

        return $this->generateLoginToken($user, TokenNames::userWebToken);
    }

    public function mobileLogin(string $usernameField, array $creds): array
    {
        $user = $this->userAccounts->getByUsername($usernameField, $creds[$usernameField]);
        if (!$user) $this->loginFailed();
        $this->validateUser($user);
        $this->tryLogin($user, $creds['pin_code'], $user->pin_code);
        $user->deleteAllTokens();

        //log history
        $user_account_id = $user->id;
        $squidpay_module = 'Mobile Login';
        $namespace = 'App\Services\Auth';
        $transaction_date = Carbon::now();
        $remarks = '';
        $operation = 'Login via mobile device';
        $reference_number = '';
        $this->loghistoryService->logUserHistory($user_account_id, $reference_number, $squidpay_module, $namespace, $transaction_date, $remarks, $operation);

        return $this->generateLoginToken($user, TokenNames::userMobileToken);
    }

    public function clientLogin(string $clientId, string $clientSecret): NewAccessToken
    {
        $client = $this->clients->getClient($clientId);

        if(!$client || !Hash::check($clientSecret, $client->client_secret)) {
            $this->invalidCredentials();
        }

        //log history
        $user_account_id = $user->id;
        $squidpay_module = 'Client Login';
        $namespace = 'App\Services\Auth';
        $transaction_date = Carbon::now();
        $remarks = '';
        $operation = 'Login via client';
        $reference_number = '';
        $this->loghistoryService->logUserHistory($user_account_id, $reference_number, $squidpay_module, $namespace, $transaction_date, $remarks, $operation);


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

    public function verify(string $userId, string $verificationType, string $otp)
    {
        if(App::environment('local')) {
            if($otp === "1111") return;
            else $this->otpInvalid('Invalid OTP.');
        }

        $identifier = $verificationType.':'.$userId;
        $otpValidity = $this->otpService->validate($identifier, $otp);
        if (!$otpValidity->status) $this->otpInvalid($otpValidity->message);
    }

    public function verifyLogin(string $usernameField, string $username, string $otp)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if (!$user) $this->accountDoesntExist();

        $this->verify($user->id, OtpTypes::login, $otp);
    }

    public function generateTransactionOTP(UserAccount $user, string $otpType)
    {
        $usernameField = $this->getUsernameFieldByAvailability($user);
        $username = $this->getUsernameByField($user, $usernameField);
        $notifService = $usernameField === UsernameTypes::MobileNumber ? $this->smsService : $this->emailService;

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

        $otp = $this->generateOTP($otpType, $user->id);
        if (App::environment('local')) return;

        $notif = $notifService == null ? $this->notificationService : $notifService;

        if ($otpType === OtpTypes::registration)
            $notif->sendAccountVerification($username, $otp->token);
        elseif ($otpType === OtpTypes::login)
            $notif->sendLoginVerification($username, $otp->token);
        elseif ($otpType === OtpTypes::passwordRecovery || $otpType === OtpTypes::pinRecovery)
            $notif->sendPasswordVerification($username, $otp->token, $otpType);
        elseif ($otpType === OtpTypes::sendMoney)
            $notif->sendMoneyVerification($username, $otp->token);
        else
            $this->otpTypeInvalid();
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */

    private function generateLoginToken(UserAccount $user, string $tokenType): array
    {
        $token = $user->createToken($tokenType);
        $latestPassword = $this->passwordHistories->getLatest($user->id);
        $latestPin = $this->pinCodeHistories->getLatest($user->id);
        $passwordAboutToExpire = $latestPassword ? $latestPassword->isAboutToExpire($this->remainingAgeToNotify, $this->maxPasswordAge) : false;
        $pinAboutToExpire = $latestPin ? $latestPin->isAboutToExpire($this->remainingAgeToNotify, $this->maxPasswordAge) : false;

        return [
            'user_token' => [
                'access_token' => $token->plainTextToken,
                'created_at' => $token->accessToken->created_at,
                'expires_in' => $this->tokenExpiration
            ],
            'notify_password_expiration' => $passwordAboutToExpire,
            'password_age' => $latestPassword ? $latestPassword->password_age : 0,
            'notify_pin_expiration' => $pinAboutToExpire,
            'pin_age' => $latestPin ? $latestPin->pin_age : 0
        ];
    }

    private function generateOTP(string $otpType, string $userId): object
    {
        if(App::environment('local')) {
            return (object) [
                'status' => true,
                'token' => "1111",
                'message' => "OTP generated",
            ];
        }

        $identifier = $otpType.':'.$userId;
        $otp = $this->otpService->generate($identifier);
        if (!$otp->status) $this->otpInvalid($otp->message);

        return $otp;
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


}
