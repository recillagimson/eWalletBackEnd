<?php


namespace App\Services\Auth\UserKey;


use App\Enums\OtpTypes;
use App\Enums\UserKeyTypes;
use App\Models\UserAccount;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserKeys\IUserKeyLogsRepository;
use App\Services\Auth\IAuthService;
use App\Services\Utilities\OTP\IOtpService;
use App\Traits\Errors\WithAuthErrors;
use Illuminate\Support\Facades\Hash;

class UserKeyService implements IUserKeyService
{
    use WithAuthErrors;

    private int $daysToResetAttempts;
    private int $remainingAgeToNotify;
    private int $minPasswordAge;
    private int $maxPasswordAge;
    private int $passwordRepeatCount;

    private IUserAccountRepository $userAccounts;
    private IUserKeyLogsRepository $keyLogs;
    private IAuthService $authService;
    private IOtpService $otpService;


    public function __construct(IAuthService $authService,
                                IOtpService $otpService,
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

        $this->userAccounts = $userAccounts;
        $this->keyLogs = $keyLogs;
    }

    public function forgotKey(string $usernameField, string $username, string $otpType)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        $this->validateUser($user);

        $this->checkKey($user, '');
        $this->authService->sendOTP($usernameField, $username, $otpType);
    }

    /**
     * Verifies and validates otp for password recovery
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otp
     * @param string $otpType
     */
    public function verifyKey(string $usernameField, string $username, string $otp, string $otpType)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        $this->validateUser($user);
        $this->authService->verify($user->id, OtpTypes::passwordRecovery, $otpType);
    }

    public function resetKey(string $usernameField, string $username, string $key, string $keyType,
                             string $otpType, bool $requireOtp = true)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        $this->validateUser($user);

        $identifier = $otpType . ':' . $user->id;
        if ($requireOtp) $this->otpService->ensureValidated($identifier);
        $this->checkKey($user, $key);

        $hashedKey = Hash::make($key);
        $user->password = $keyType === UserKeyTypes::password ? $hashedKey : $user->password;
        $user->pin_code = $keyType === UserKeyTypes::pin ? $hashedKey : $user->pin_code;
        $user->save();

        $this->keyLogs->log($user->id, $hashedKey);
    }


    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */

    private function validateUser(UserAccount $user)
    {
        if (!$user || !$user->verified) $this->accountDoesntExist();
        if ($user->is_lockout) $this->accountLockedOut();
    }

    private function checkKey(string $userId, string $key)
    {
        $latestKey = $this->keyLogs->getLatest($userId);
        if (!$latestKey->isAtMinimumAge($this->minPasswordAge)) $this->passwordNotAged($this->minPasswordAge);

        if ($key) {
            $keyLogs = $this->keyLogs->getPrevious($this->passwordRepeatCount, $userId);
            foreach ($keyLogs as $log) {
                $exists = Hash::check($key, $log->key);
                if ($exists === true) $this->passwordUsed();
            }
        }
    }

}
