<?php

namespace App\Services\UserAccount;

use App\Enums\OtpTypes;
use App\Enums\UsernameTypes;
use App\Models\UserAccount;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserAccountNumber\IUserAccountNumberRepository;
use App\Repositories\UserUtilities\TempUserDetail\ITempUserDetailRepository;
use App\Services\Auth\IAuthService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithUserErrors;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserAccountService implements IUserAccountService
{
    use WithUserErrors, WithAuthErrors;

    public IUserAccountRepository $users;
    private IOtpService $otpService;
    private IEmailService $emailService;
    private IUserAccountNumberRepository $userAccountNumbers;
    private ITempUserDetailRepository $tempUserDetail;
    private IAuthService $authService;
    private ISmsService $smsService;

    public function __construct(IUserAccountRepository $users,
                                IUserAccountNumberRepository $userAccountNumbers,
                                IOtpService $otpService,
                                IEmailService $emailService,
                                ISmsService $smsService,
                                IAuthService $authService,
                                ITempUserDetailRepository $tempUserDetail)
    {
        $this->users = $users;
        $this->userAccountNumbers = $userAccountNumbers;
        $this->otpService = $otpService;
        $this->emailService = $emailService;
        $this->tempUserDetail = $tempUserDetail;
        $this->authService = $authService;
        $this->smsService = $smsService;
    }

    public function getAdminUsers(): Collection
    {
        return $this->users->getAdminUsers();
    }

    public function getAdminUsersByEmail(string $email): Collection
    {
        return $this->users->getAdminUsersByEmail($email);
    }

    public function getAdminUsersByName(string $lastName, string $firstName): Collection
    {
        return $this->users->getAdminUsersByName($lastName, $firstName);
    }

    public function createAdminUser(array $userInfo, string $userCreated): UserAccount
    {
        try {
            DB::beginTransaction();

            $tempPassword = Str::random(12);
            $userData = [
                'account_number' => $this->userAccountNumbers->generateNo('A'),
                'email' => $userInfo['email'],
                'password' => Hash::make($tempPassword),
                'is_admin' => true,
                'user_created' => $userCreated,
                'is_active' => true,
                'verified' => true
            ];

            $userExists = $this->users->getByUsername('email', $userData['email']);
            if ($userExists) $this->accountAlreadyTaken();

            $newUser = $this->users->create($userData);
            $userInfo['user_created'] = $userCreated;
            $newUser->profile()->create($userInfo);

            $this->emailService->sendAdminUserAccountDetails($userInfo['email'], $userInfo['first_name'],
                $userData['email'], $tempPassword);

            DB::commit();
            return $newUser->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateAdminUser(string $id, array $userInfo, string $userUpdated): UserAccount
    {
        try {
            DB::beginTransaction();

            $user = $this->users->getUser($id);
            if (!$user) $this->accountDoesntExist();

            $userInfo['user_updated'] = $userUpdated;
            $user->update($userInfo);
            $user->profile->update($userInfo);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteAdminUser(string $id)
    {
        $user = $this->users->getUser($id);
        if (!$user) $this->accountDoesntExist();
        $user->delete();
    }

    public function getAllPaginated(array $attributes, $perPage = 10) {

        return $this->users->getAllUsersPaginated($attributes, $perPage);
    }

    public function findById(string $id) {

        $result = $this->users->findById($id);

        if (!$result) {
            throw ValidationException::withMessages([
                'user_not_found' => 'User Account not found'
            ]);
        }

        return $result;
    }

    public function validateEmail(string $userId, string $email)
    {
        $user = $this->users->get($userId);
        if (!$user) $this->accountDoesntExist();
        $this->checkEmail($user, $email);

        $otp = $this->authService->generateOTP(OtpTypes::updateEmail, $user->id, $user->otp_enabled);
        $this->emailService->updateEmailVerification($email, $otp->token);
    }

    public function updateEmail(string $email, object $user): array
    {
        $user = $this->users->get($user->id);
        if (!$user) $this->accountDoesntExist();
        $this->checkEmail($user, $email);

        $identifier = OtpTypes::updateEmail . ':' . $user->id;
        $this->otpService->ensureValidated($identifier, $user->otp_enabled);

        $this->users->update($user, [
            'email' => $email
        ]);

        return $this->updateEmailResponse($email);
    }

    public function validateMobile(string $userId, string $mobile)
    {
        $user = $this->users->get($userId);
        if (!$user) $this->accountDoesntExist();
        $this->checkMobile($user, $mobile);

        $otp = $this->authService->generateOTP(OtpTypes::updateMobile, $user->id, $user->otp_enabled);
        $this->smsService->updateMobileVerification($mobile, $otp->token);
    }

    public function updateMobile(string $userId, string $mobile, UserAccount $user): array
    {
        $user = $this->users->get($userId);
        if (!$user) $this->accountDoesntExist();
        $this->checkMobile($user, $mobile);

        $identifier = OtpTypes::updateMobile . ':' . $user->id;
        $this->otpService->ensureValidated($identifier, $user->otp_enabled);

        $this->users->update($user, [
            UsernameTypes::MobileNumber => $mobile
        ]);

        return $this->updateMobileResponse(UsernameTypes::MobileNumber, $mobile);
    }

    public function toggleActivation(string $userId): array
    {
        $user = $this->users->get($userId);
        if (!$user) $this->userAccountNotFound();

        $user->toggleActivation();

        return $this->getToggleResponse($userId, 'is_active', $user->is_active);
    }

    public function toggleLockout(string $userId): array
    {
        $user = $this->users->get($userId);
        if (!$user) $this->userAccountNotFound();

        $user->toggleLockout();

        return $this->getToggleResponse($userId, 'is_lockout', $user->is_lockout);
    }

    private function getToggleResponse(string $id, string $field, bool $value): array
    {
        return [
            'id' => $id,
            $field => $value
        ];
    }

    private function updateEmailResponse($email): array
    {
        return [
            'email' => $email,
        ];
    }

    private function updateMobileResponse($mobileField, $mobile): array
    {
        return [
            $mobileField => $mobile,
        ];
    }

    private function checkEmail(UserAccount $user, string $email)
    {
        $existingUser = $this->users->getByUsername(UsernameTypes::Email, $email);
        if ($existingUser) {
            if ($existingUser->id !== $user->id) $this->emailAlreadyTaken();
        }
    }

    private function checkMobile(UserAccount $user, string $mobileNumber)
    {
        $existingUser = $this->users->getByUsername(UsernameTypes::MobileNumber, $mobileNumber);
        if ($existingUser) {
            if ($existingUser->id !== $user->id) $this->mobileAlreadyTaken();
        }
    }


}
