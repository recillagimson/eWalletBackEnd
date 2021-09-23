<?php


namespace App\Services\Auth\UserKey;


use App\Enums\UserKeyTypes;
use App\Enums\UsernameTypes;
use App\Models\UserAccount;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserKeys\IUserKeyLogsRepository;
use App\Services\Auth\IAuthService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithErrors;
use App\Traits\RouteParamHelpers;
use App\Traits\UserHelpers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserKeyService implements IUserKeyService
{
    use WithErrors, WithAuthErrors, UserHelpers, RouteParamHelpers;

    private int $daysToResetAttempts;
    private int $remainingAgeToNotify;
    private int $minPasswordAge;
    private int $maxPasswordAge;
    private int $passwordRepeatCount;

    private IUserAccountRepository $userAccounts;
    private IUserKeyLogsRepository $keyLogs;
    private IAuthService $authService;
    private IOtpService $otpService;
    private IEmailService $emailService;
    private ISmsService $smsService;


    public function __construct(IAuthService $authService,
                                IOtpService $otpService,
                                IEmailService $emailService,
                                ISmsService $smsService,
                                IUserAccountRepository $userAccounts,
                                IUserKeyLogsRepository $keyLogs)
    {
        $this->daysToResetAttempts = config('auth.account_lockout_attempt_reset');
        $this->remainingAgeToNotify = config('auth.password_notify_expire');
        $this->minPasswordAge = config('auth.password_min_age');
        $this->maxPasswordAge = config('auth.password_max_age_np');
        $this->passwordRepeatCount = config('auth.password_repeat_count');

        $this->authService = $authService;
        $this->otpService = $otpService;
        $this->emailService = $emailService;
        $this->smsService = $smsService;

        $this->userAccounts = $userAccounts;
        $this->keyLogs = $keyLogs;
    }

    public function validateKey(string $userId, string $currentKey, string $newKey, string $keyType,
                                bool $requireOtp = true)
    {
        $user = $this->userAccounts->get($userId);
        $this->validateUser($user, $keyType, $currentKey);
        $this->checkKey($user->id, $newKey);

        if ($requireOtp) {
            $otpType = $this->getOtpTypeFromUserKeyType($keyType);
            $usernameField = $this->getUsernameFieldByAvailability($user);
            $username = $usernameField === UsernameTypes::MobileNumber ? $user->mobile_number : $user->email;
            $notifService = $usernameField === UsernameTypes::MobileNumber ? $this->smsService : $this->emailService;
            $this->authService->sendOTP($usernameField, $username, $otpType, $notifService);
        }
    }

    public function forgotKey(string $usernameField, string $username, string $otpType)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        $this->validateUser($user);

        $this->checkKey($user->id, '');
        $this->authService->sendOTP($usernameField, $username, $otpType);
    }

    public function verifyKey(string $usernameField, string $username, string $otp, string $otpType)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        $this->validateUser($user);
        $this->authService->verify($user->id, $otpType, $otp, $user->otp_enabled);
    }

    public function resetKey(string $usernameField, string $username, string $key, string $keyType,
                             string $otpType, bool $requireOtp = true)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        $this->validateUser($user);

        $identifier = $otpType . ':' . $user->id;
        if ($requireOtp) $this->otpService->ensureValidated($identifier, $user->otp_enabled);
        $this->checkKey($user->id, $key);
        $this->updateKey($user, $keyType, $key);
    }

    public function changeKey(string $userId, string $currentKey, string $newKey, string $keyType, string $otpType,
                              bool $requireOtp = true)
    {
        $user = $this->userAccounts->get($userId);
        $this->validateUser($user, $keyType, $currentKey);
        $this->checkKey($user->id, $newKey);

        if (!$user->is_admin) {
            $identifier = $otpType . ':' . $user->id;
            if ($requireOtp) $this->otpService->ensureValidated($identifier, $user->otp_enabled);
        }

        $this->updateKey($user, $keyType, $newKey);
    }


    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Validates a user
     *
     * @param UserAccount|null $user
     * @param string|null $keyType User account key type (password / pin). Supply a value to this parameter to match
     * $userKey with the user account. Optional.
     * @param string|null $userKey User account key (password / pin code) to match with the user.  Optional.
     * @throws ValidationException
     */
    private function validateUser(?UserAccount $user, string $keyType = null, string $userKey = null)
    {
        if (!$user) $this->accountDoesntExist();
        if (!$user->verified) $this->accountDoesntExist();
        if ($user->is_lockout) $this->accountLockedOut();

        if ($keyType) {
            if ($userKey) {
                $accountKey = $keyType === UserKeyTypes::pin ? $user->pin_code : $user->password;
                $keyField = $this->getKeyFieldFromUserKeyType($keyType);
                $keyMatched = Hash::check($userKey, $accountKey);
                if (!$keyMatched) {
                    $this->validationError($keyField, 'Incorrect ' . $keyType);
                }
            }
        }
    }

    private function checkKey(string $userId, string $key)
    {
        $latestKey = $this->keyLogs->getLatest($userId);
        if ($latestKey) {
            if (!$latestKey->isAtMinimumAge($this->minPasswordAge)) $this->passwordNotAged($this->minPasswordAge);
        }

        if ($key) {
            $keyLogs = $this->keyLogs->getPrevious($this->passwordRepeatCount, $userId);
            foreach ($keyLogs as $log) {
                $exists = Hash::check($key, $log->key);
                if ($exists === true) $this->passwordUsed();
            }
        }
    }

    private function updateKey(UserAccount $user, string $keyType, string $newKey)
    {
        $hashedKey = Hash::make($newKey);
        $user->password = $keyType === UserKeyTypes::password ? $hashedKey : $user->password;
        $user->pin_code = $keyType === UserKeyTypes::pin ? $hashedKey : $user->pin_code;
        $user->save();

        $this->keyLogs->log($user->id, $hashedKey);
    }


}
