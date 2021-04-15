<?php

namespace App\Services\Auth;

use App\Enums\ErrorCodes;
use App\Enums\OtpTypes;
use App\Enums\TokenNames;
use App\Models\UserAccount;
use App\Repositories\Client\IClientRepository;
use App\Repositories\PasswordHistory\IPasswordHistoryRepository;
use App\Repositories\PinCodeHistory\IPinCodeHistoryRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\OTP\IOtpService;
use Illuminate\Support\Facades\Hash;
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
    private int $passwordRepeatCount;

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
        $this->passwordRepeatCount = config('auth.password_repeat_count');
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
     * provided credentials when using a web client
     *
     * @param string $usernameField
     * @param array $creds
     * @param string $ip
     * @return array
     * @throws ValidationException
     */
    public function login(string $usernameField, array $creds, string $ip): array
    {
        $user = $this->userAccounts->getByUsername($usernameField, $creds[$usernameField]);
        if(!$user) $this->loginFailed();
        $this->validateUser($user);

        $passwordMatched = Hash::check($creds['password'], $user->password);
        if(!$passwordMatched) {
            $user->updateLockout($this->maxLoginAttempts);
            $this->loginFailed();
        }

        $user->deleteAllTokens();
        return $this->generateLoginToken($user, TokenNames::userWebToken);
    }

    /**
     * Attempts to authenticate the user with the
     * provided credentials when using a mobile apps.
     *
     * @param string $usernameField
     * @param array $creds
     * @return array
     * @throws ValidationException
     */
    public function mobileLogin(string $usernameField, array $creds): array
    {
        $user = $this->userAccounts->getByUsername($usernameField, $creds[$usernameField]);
        if(!$user) $this->loginFailed();
        $this->validateUser($user);

        $passwordMatched = Hash::check($creds['pin_code'], $user->pin_code);
        if(!$passwordMatched) {
            $user->updateLockout($this->maxLoginAttempts);
            $this->loginFailed();
        }

        $user->deleteAllTokens();
        return $this->generateLoginToken($user, TokenNames::userMobileToken);
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
        $this->checkPassword($user, '');

        $otp = $this->otpService->generate(OtpTypes::passwordRecovery.':'.$user->id);
        if(!$otp->status) $this->invalidOtp($otp->message);

        $userArray = $user->toArray();
        $this->notificationService->sendPasswordVerification($userArray[$usernameField], $otp->token);
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

        $identifier = OtpTypes::passwordRecovery.':'.$user->id;
        $this->otpService->ensureValidated($identifier);

        $this->checkPassword($user, $password);

        $hashedPassword = Hash::make($password);
        $user->password = $hashedPassword;
        $user->save();

        $this->passwordHistories->log($user->id, $hashedPassword);
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
    public function verifyLogin(string $usernameField, string $username, string $otp)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if(!$user) $this->accountDoesntExist();

        $this->verify($user->id, OtpTypes::login, $otp);
    }

    /**
     * Verifies and validates otp for password recovery
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otp
     * @throws ValidationException
     */
    public function verifyPassword(string $usernameField, string $username, string $otp)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if(!$user) $this->accountDoesntExist();

        $this->verify($user->id, OtpTypes::passwordRecovery, $otp);
    }

    /**
     * Generates an OTP for mobile login
     *
     * @param string $usernameField
     * @param string $username
     * @throws ValidationException
     */
    public function generateMobileLoginOTP(string $usernameField, string $username)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if(!$user) $this->accountDoesntExist();

        $identifier = OtpTypes::login.':'.$user->id;
        $otp = $this->otpService->generate($identifier);
        if(!$otp->status) $this->invalidOtp($otp->message);

        $this->notificationService->sendLoginVerification($username, $otp->token);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */

    private function checkPassword(UserAccount $user, string $password)
    {
        $latesPassword = $this->passwordHistories->getLatest($user->id);
        if(!$latesPassword->isAtMinimumAge($this->minPasswordAge)) $this->passwordNotAged();

        if($password)
        {
            $passwordHistories = $this->passwordHistories->getPrevious($this->passwordRepeatCount, $user->id);
            foreach($passwordHistories as $passwordHistory)
            {
                $exists = Hash::check($password, $passwordHistory->password);
                if($exists === true) $this->passwordUsed();
            }
        }
    }

    private function generateLoginToken(UserAccount $user,  string $tokenType): array
    {
        $token = $user->createToken($tokenType);
        $latestPassword = $this->passwordHistories->getLatest($user->id);
        $latesPin = $this->pinCodeHistories->getLatest($user->id);
        $passwordAboutToExpire = $latestPassword ? $latestPassword->isAboutToExpire($this->remainingAgeToNotify, $this->maxPasswordAge) : false;
        $pinAboutToExpire = $latesPin ? $latesPin->isAboutToExpire($this->remainingAgeToNotify, $this->maxPasswordAge) : false;

        return [
            'user_token' => [
                'access_token' => $token->plainTextToken,
                'created_at' => $token->accessToken->created_at,
                'expires_in' => $this->tokenExpiration
            ],
            'notify_password_expiration' => $passwordAboutToExpire,
            'password_age' => $latestPassword ? $latestPassword->password_age : null,
            'notify_pin_expiration' => $pinAboutToExpire,
            'pin_age' => $latesPin ? $latesPin->pin_age : null
        ];
    }

    private function validateUser(UserAccount $user)
    {
        if(!$user->verified) $this->accountUnverified();
        if($user->is_lockout) $this->accountLockedOut();

        $user->resetLoginAttempts($this->daysToResetAttempts);
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */

    private function loginFailed()
    {
        throw ValidationException::withMessages([
            'error_code' => ErrorCodes::LoginFailed,
            'account' => 'Login Failed.'
        ]);
    }

    private function accountUnverified()
    {
        throw ValidationException::withMessages([
            'error_code' => ErrorCodes::UnverifiedAccount,
            'account' => 'Unverified Account.'
        ]);
    }

    private function accountDoesntExist()
    {
        throw ValidationException::withMessages([
            'error_code' => ErrorCodes::AccountDoesNotExist,
            'account' => 'Account does not exists'
        ]);
    }

    private function invalidCredentials()
    {
        throw ValidationException::withMessages([
            'error_code' => ErrorCodes::InvalidClient,
            'client' => 'Invalid Client Credentials'
        ]);
    }

    private function accountLockedOut()
    {
        throw ValidationException::withMessages([
            'error_code' => ErrorCodes::AccountLockedOut,
            'account' => 'Account has been locked out. Due to 3 failed login attempts.'
        ]);
    }

    private function invalidOtp(string $message)
    {
        throw ValidationException::withMessages([
            'error_code' => ErrorCodes::OTPInvalid,
            'code' => $message
        ]);
    }

    private function passwordUsed()
    {
        throw ValidationException::withMessages([
            'error_code' => ErrorCodes::PasswordUsed,
            'password' => 'Password has already been used.'
        ]);
    }

    private function passwordNotAged()
    {
        throw ValidationException::withMessages([
            'error_code' => ErrorCodes::PasswordNotAged,
            'password' => 'Password cannot be changed for at least '.$this->minPasswordAge.'.'
        ]);
    }
}
