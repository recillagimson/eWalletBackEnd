<?php

namespace App\Services\UserAccount;

use App\Enums\OtpTypes;
use App\Models\UserAccount;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\OTP\IOtpService;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithUserErrors;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserAccountService implements IUserAccountService
{
    use WithUserErrors, WithAuthErrors;

    public IUserAccountRepository $users;
    private IOtpService $otpService;
    private IEmailService $emailService;

    public function __construct(IUserAccountRepository $users,
                                IOtpService $otpService,
                                IEmailService $emailService)
    {
        $this->users = $users;
        $this->otpService = $otpService;;
        $this->emailService = $emailService;
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
                'email' => $userInfo['email'],
                'password' => Hash::make($tempPassword),
                'is_admin' => true,
                'user_created' => $userCreated,
                'is_active' => true,
                'verified' => false
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

    public function updateEmail(string $emailField, string $email, UserAccount $user): array
    {

        $identifier = OtpTypes::updateEmail . ':' . $user->id;
        $this->otpService->ensureValidated($identifier);

        $this->validateEmail($emailField, $email);

        $this->users->update($user, [
            $emailField => $email
        ]);

        return $this->updateEmailResponse($emailField, $email);
    }

    public function validateEmail(string $emailField, string $email)
    {
        $user = $this->users->getByEmail($emailField, $email);
        if (!$user) return;

        if ($user->verified) $this->emailAlreadyTaken();
        $user->forceDelete();
    }

    public function updateMobile(string $mobileField, string $mobile, UserAccount $user): array
    {

        $identifier = OtpTypes::updateMobile . ':' . $user->id;
        $this->otpService->ensureValidated($identifier);

        $this->validateMobile($mobileField, $mobile);

        $this->users->update($user, [
            $mobileField => $mobile
        ]);

        return $this->updateMobileResponse($mobileField, $mobile);
    }

    public function validateMobile(string $mobileField, string $mobile) {
        $user = $this->users->getByUsername($mobileField, $mobile);
        if (!$user) return;

        if ($user->verified) $this->mobileAlreadyTaken();
        $user->forceDelete();
    }

    private function updateEmailResponse($emailField, $email): array
    {
        return [
            $emailField => $email,
        ];
    }

    private function updateMobileResponse($mobileField, $mobile): array
    {
        return [
            $mobileField => $mobile,
        ];
    }



}
