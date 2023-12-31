<?php


namespace App\Services\v2\Auth\Registration;


use App\Enums\AccountTiers;
use App\Enums\Currencies;
use App\Enums\OtpTypes;
use App\Repositories\QrTransactions\IQrTransactionsRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserAccountNumber\IUserAccountNumberRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserKeys\PasswordHistory\IPasswordHistoryRepository;
use App\Repositories\UserKeys\PinCodeHistory\IPinCodeHistoryRepository;
use App\Services\v2\Auth\IAuthService;
use App\Traits\Errors\WithAuthErrors;
use Illuminate\Support\Facades\Hash;

class RegistrationService implements IRegistrationService
{
    use WithAuthErrors;

    private IAuthService $authService;

    private IUserAccountRepository $userAccounts;
    private IUserAccountNumberRepository $userAccountNumbers;
    private IUserBalanceInfoRepository $userBalances;
    private IPasswordHistoryRepository $passwordHistories;
    private IPinCodeHistoryRepository $pinCodeHistories;
    private IQrTransactionsRepository $qrTransactions;

    public function __construct(IAuthService $authService,
                                IUserAccountRepository $userAccounts,
                                IUserAccountNumberRepository $userAccountNumbers,
                                IUserBalanceInfoRepository $userBalances,
                                IPasswordHistoryRepository $passwordHistories,
                                IPinCodeHistoryRepository $pinCodeHistories,
                                IQrTransactionsRepository $qrTransactions)
    {
        $this->authService = $authService;

        $this->userAccounts = $userAccounts;
        $this->userAccountNumbers = $userAccountNumbers;
        $this->userBalances = $userBalances;
        $this->passwordHistories = $passwordHistories;
        $this->pinCodeHistories = $pinCodeHistories;
        $this->qrTransactions = $qrTransactions;
    }

    // public function validateAccount(string $usernameField, string $username)
    // {
    //     $user = $this->userAccounts->getByUsername($usernameField, $username);
    //     if (!$user) return;

    //     if ($user->verified) $this->accountAlreadyTaken();
    //     $user->forceDelete();
    // }

    // public function register(array $newUser, string $usernameField)
    // {
    //     $this->validateAccount($usernameField, $newUser[$usernameField]);

    //     $newUser['password'] = Hash::make($newUser['password']);
    //     $newUser['tier_id'] = AccountTiers::tier1;
    //     $newUser['account_number'] = $this->userAccountNumbers->generateNo();

    //     $user = $this->userAccounts->create($newUser);

    //     //LOG INITIAL CHANGES TO USER KEYS (PASSWORD / PIN CODES)
    //     $this->passwordHistories->log($user->id, $newUser['password']);
    //     // $this->pinCodeHistories->log($user->id, $newUser['pin_code']);

    //     $this->authService->sendOTP($usernameField, $newUser[$usernameField], OtpTypes::registration);
    //     return $user;
    // }

    // public function verifyAccount(string $usernameField, string $username, string $otp)
    // {
    //     $user = $this->userAccounts->getByUsername($usernameField, $username);
    //     if (!$user) $this->accountDoesntExist();

    //     if ($user->verified) $this->accountAlreadyVerified();

    //     $this->authService->verify($user->id, OtpTypes::registration, $otp, $user->otp_enabled);

    //     return $user;
    // }

    // public function registerPin(array $data, string $usernameField)
    // {
    //     $user = $this->userAccounts->getByUsername($usernameField, $data[$usernameField]);
    //     if (!$user) $this->accountDoesntExist();

    //     if ($user->verified) $this->accountAlreadyVerified();

    //     $user->pin_code = Hash::make($data['pin_code']);
    //     $user->verified = true;
    //     $user->save();

    //     $this->pinCodeHistories->log($user->id, $data['pin_code']);

    //     $this->setupAccount($user->id);

    //     $token = $this->authService->mobileLogin($usernameField, $data);

    //     return $token;
    // }

    // /*
    // |--------------------------------------------------------------------------
    // | PRIVATE METHODS
    // |--------------------------------------------------------------------------
    // */

    // private function setupAccount(string $userId)
    // {
    //     $this->setupUserBalance($userId);
    //     $this->setupQrTransaction($userId);
    // }

    // private function setupUserBalance(string $userId)
    // {
    //     $balance = [
    //         'user_account_id' => $userId,
    //         'currency_id' => Currencies::philippinePeso,
    //         'user_created' => $userId,
    //         'user_updated' => $userId,
    //     ];

    //     $this->userBalances->create($balance);
    // }

    // private function setupQrTransaction(string $userId)
    // {
    //     $qrTransactions = [
    //         'user_account_id' => $userId,
    //         'amount' => 0,
    //         'status' => 1,
    //         'user_created' => $userId
    //     ];

    //     $this->qrTransactions->create($qrTransactions);
    // }
}
