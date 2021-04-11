<?php

namespace App\Services\Auth;

use App\Enums\OtpTypes;
use App\Enums\TokenNames;
use App\Models\UserAccount;
use App\Repositories\Client\IClientRepository;
use App\Repositories\PasswordHistory\IPasswordHistoryRepository;
use App\Repositories\PinCodeHistory\IPinCodeHistoryRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\OTP\IOtpService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Validation\ValidationException;

class AuthService implements IAuthService
{
    public IUserAccountRepository $userAccounts;
    public IClientRepository $clients;
    public IPasswordHistoryRepository $passwordHistories;
    public IPinCodeHistoryRepository $pinCodeHistories;

    private INotificationService $notificationService;
    private IOtpService $otpService;

    private int $maxLoginAttempts;
    private int $daysToResetAttempts;

    private int $remainingAgeToNotify;
    private int $minPasswordAge;
    private int $maxPasswordAge;
    private int $tokenExpiration;


    public function __construct(IUserAccountRepository $userAccts,
                                IPasswordHistoryRepository $passwordHistories,
                                IPinCodeHistoryRepository $pinCodeHistories,
                                IClientRepository $clients,
                                INotificationService $notificationService,
                                IOtpService $otpService)
    {
        $this->userAccounts = $userAccts;
        $this->clients = $clients;
        $this->passwordHistories = $passwordHistories;
        $this->pinCodeHistories = $pinCodeHistories;

        $this->otpService = $otpService;
        $this->notificationService = $notificationService;

        $this->maxLoginAttempts = config('auth.account_lockout_attempt');
        $this->daysToResetAttempts = config('auth.account_lockout_attempt_reset');
        $this->remainingAgeToNotify = config('auth.password_notify_expire');
        $this->minPasswordAge = config('auth.password_min_age');
        $this->maxPasswordAge = config('auth.password_max_age_np');
        $this->tokenExpiration = config('sanctum.expiration');
    }


    /**
     * Creates a new UserAccount record
     *
     * @param array $newUser
     * @param string $usernameField
     * @return mixed
     * @throws ValidationException
     */
    public function register(array $newUser, string $usernameField)
    {
        $newUser['password'] = Hash::make($newUser['password']);
        $newUser['pin_code'] = Hash::make($newUser['pin_code']);
        $user = $this->userAccounts->create($newUser);

        $this->passwordHistories->log($user->id, $newUser['password']);
        $this->pinCodeHistories->log($user->id, $newUser['pin_code']);

        $identifier = OtpTypes::registration.':'.$user->id;
        $otp = $this->otpService->generate($identifier);
        if(!$otp->status) $this->invalidOtp($otp->message);

        $this->notificationService->sendAccountVerification($newUser[$usernameField], $otp->token);
        return $user;
    }

    /**
     * Attempts to authenticate the user with the
     * provided credentials
     *
     * @param string $usernameField
     * @param array $creds
     * @param string $ip
     * @throws ValidationException
     */
    public function login(string $usernameField, array $creds, string $ip)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $creds[$usernameField]);
        if(!$user) $this->loginFailed();
        if(!$user->verified) $this->accountUnverified();
        if($user->is_lockout) $this->accountLockedOut();

        $user->resetLoginAttempts($this->daysToResetAttempts);

        $passwordMatched = Hash::check($creds['password'], $user->password);
        if(!$passwordMatched) {
            $user->updateLockout($this->maxLoginAttempts);
            $this->loginFailed();
        }

        $identifier = OtpTypes::login.':'.$user->id;
        $otp = $this->otpService->generate($identifier);
        if(!$otp->status) $this->invalidOtp($otp->message);

        $this->notificationService->sendLoginVerification($creds[$usernameField], $otp->token);
    }

    /**
     * Authenticates Client Applications
     *
     * @param string $clientId
     * @param string $clientSecret
     * @return NewAccessToken
     * @throws ValidationException
     */
    public function clientLogin(string $clientId, string $clientSecret): NewAccessToken
    {
        $client = $this->clients->getClient($clientId);

        if(!$client || !Hash::check($clientSecret, $client->client_secret))
        {
            $this->invalidCredentials();
        }

        return $client->createToken(TokenNames::clientToken);
    }

    /**
     * Generates OTP for password recovery
     *
     *
     * @param string $usernameField
     * @param string $username
     * @throws ValidationException
     */
    public function forgotPassword(string $usernameField, string $username)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if(!$user) $this->accountDoesntExist();


        $otp = $this->otpService->generate(OtpTypes::passwordRecovery.':'.$user->id);
        if(!$otp->status) $this->invalidOtp($otp->message);
        $userArray = $user->toArray();
        $this->notificationService->sendPasswordVerification($userArray[$usernameField], $otp->token);
    }

    /**
     * Verifies the validity of OTPs
     *
     *
     * @param string $userId
     * @param string $verificationType
     * @param string $otp
     * @throws ValidationException
     */
    public function verify(string $userId, string $verificationType, string $otp)
    {
        $identifier = $verificationType.':'.$userId;
        $otpValidity = $this->otpService->validate($identifier, $otp);
        if(!$otpValidity->status) $this->invalidOtp($otpValidity->message);
    }

    /**
     * Activates the user account by validating the otp
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otp
     * @return mixed
     * @throws ValidationException
     */
    public function verifyAccount(string $usernameField, string $username, string $otp)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if(!$user) $this->accountDoesntExist();

        $this->verify($user->id, OtpTypes::registration, $otp);

        $user->verified = true;
        $user->save();

        return $user;
    }

    /**
     * Validate OTP and provides user token
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otp
     * @return array
     * @throws ValidationException
     */
    public function verifyLogin(string $usernameField, string $username, string $otp): array
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if(!$user) $this->accountDoesntExist();

        $this->verify($user->id, OtpTypes::login, $otp);
        $user->verified = true;
        $user->save();

        $token = $user->createToken(TokenNames::userToken);
        $latestPassword = $this->passwordHistories->getLatest($user->id);
        $latesPin = $this->pinCodeHistories->getLatest($user->id);

        return [
            'user_token' => [
                'access_token' => $token->plainTextToken,
                'created_at' => $token->accessToken->created_at,
                'expires_in' => $this->tokenExpiration
            ],
            'notify_password_expiration' => $latestPassword->isAboutToExpire($this->remainingAgeToNotify, $this->maxPasswordAge),
            'password_age' => $latestPassword->password_age,
            'notify_pin_expiration' => $latesPin->isAboutToExpire($this->remainingAgeToNotify, $this->maxPasswordAge),
            'pin_age' => $latesPin->pin_age
        ];
    }

    /**
     * Reset forgotten password
     *
     * @param string $usernameField
     * @param string $username
     * @param string $password
     * @throws ValidationException
     */
    public function resetPassword(string $usernameField, string $username, string $password)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if(!$user) $this->accountDoesntExist();

        $hashedPassword = Hash::make($password);
        $user->password = $hashedPassword;
        $user->save();
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */

    private function loginFailed()
    {
        throw ValidationException::withMessages([
            'account' => 'Login Failed.'
        ]);
    }

    private function accountUnverified()
    {
        throw ValidationException::withMessages([
            'account' => 'Unverified Account.'
        ]);
    }

    private function accountDoesntExist()
    {
        throw ValidationException::withMessages([
            'account' => 'Account does not exists'
        ]);
    }

    private function invalidCredentials()
    {
        throw ValidationException::withMessages([
            'client' => 'Invalid Client Credentials'
        ]);
    }

    private function accountLockedOut()
    {
        throw ValidationException::withMessages([
            'account' => 'Account has been locked out. Due to 3 failed attempts.'
        ]);
    }

    private function invalidOtp(string $message)
    {
        throw ValidationException::withMessages([
            'code' => $message
        ]);
    }
}
