<?php


namespace App\Services\Auth\Registration;


use App\Enums\AccountTiers;
use App\Enums\Currencies;
use App\Enums\OtpTypes;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserKeys\PasswordHistory\IPasswordHistoryRepository;
use App\Repositories\UserKeys\PinCodeHistory\IPinCodeHistoryRepository;
use App\Services\Auth\IAuthService;
use App\Traits\Errors\WithAuthErrors;
use Illuminate\Support\Facades\Hash;

class RegistrationService implements IRegistrationService
{
    use WithAuthErrors;

    private IAuthService $authService;

    private IUserAccountRepository $userAccounts;
    private IUserBalanceInfoRepository $userBalances;
    private IPasswordHistoryRepository $passwordHistories;
    private IPinCodeHistoryRepository $pinCodeHistories;

    public function __construct(IAuthService $authService,
                                IUserAccountRepository $userAccounts,
                                IUserBalanceInfoRepository $userBalances,
                                IPasswordHistoryRepository $passwordHistories,
                                IPinCodeHistoryRepository $pinCodeHistories)
    {
        $this->authService = $authService;

        $this->userAccounts = $userAccounts;
        $this->userBalances = $userBalances;
        $this->passwordHistories = $passwordHistories;
        $this->pinCodeHistories = $pinCodeHistories;
    }

    public function validateAccount(string $username, string $usernameField)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if (!$user) return;

        if ($user->verified) $this->accountAlreadyTaken();
        $user->forceDelete();
    }

    public function register(array $newUser, string $usernameField)
    {
        $this->validateAccount($usernameField, $newUser[$usernameField]);

        $newUser['password'] = Hash::make($newUser['password']);
        $newUser['pin_code'] = Hash::make($newUser['pin_code']);
        $newUser['tier_id'] = AccountTiers::tier1;

        $user = $this->userAccounts->create($newUser);

        //LOG INITIAL CHANGES TO USER KEYS (PASSWORD / PIN CODES)
        $this->passwordHistories->log($user->id, $newUser['password']);
        $this->pinCodeHistories->log($user->id, $newUser['pin_code']);

        $this->authService->sendOTP($usernameField, $newUser[$usernameField], OtpTypes::registration);
        return $user;
    }

    public function verifyAccount(string $usernameField, string $username, string $otp)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $username);
        if (!$user) $this->accountDoesntExist();

        $this->authService->verify($user->id, OtpTypes::registration, $otp);

        $user->verified = true;
        $user->save();

        $this->setupAccount($user->id);

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */

    private function setupAccount(string $userId)
    {
        $this->setupUserBalance($userId);
    }

    private function setupUserBalance(string $userId)
    {
        $balance = [
            'user_account_id' => $userId,
            'currency_id' => Currencies::philippinePeso,
            'user_created' => $userId,
            'user_updated' => $userId,
        ];

        $this->userBalances->create($balance);
    }
}
