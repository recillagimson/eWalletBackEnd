<?php

namespace App\Services\UserAccount;

use App\Repositories\UserAccount\IUserAccountRepository;
use App\Traits\Errors\WithUserErrors;
use App\Enums\OtpTypes;
use App\Services\Utilities\OTP\IOtpService;

class UserAccountService implements IUserAccountService
{
    use WithUserErrors;
    
    public IUserAccountRepository $userAccountRepository;
    private IOtpService $otpService;

    public function __construct(IUserAccountRepository $userAccountRepository, IOtpService $otpService)
    {
        $this->userAccountRepository = $userAccountRepository;
        $this->otpService = $otpService;;
    }

    public function updateEmail(string $emailField, string $email, object $user) {

        $identifier = OtpTypes::updateEmail . ':' . $user->id;
        $this->otpService->ensureValidated($identifier);

        $this->validateEmail($emailField, $email);

        $this->userAccountRepository->update($user, [
            $emailField => $email
        ]);

        return $this->updateEmailResponse($emailField, $email);
    }

    public function validateEmail(string $emailField, string $email) {
        $user = $this->userAccountRepository->getByEmail($emailField, $email);
        if (!$user) return;

        if ($user->verified) $this->emailAlreadyTaken();
        $user->forceDelete();
    }

    public function updateMobile(string $mobileField, string $mobile, object $user) {

        $identifier = OtpTypes::updateMobile . ':' . $user->id;
        $this->otpService->ensureValidated($identifier);

        $this->validateMobile($mobileField, $mobile);

        $this->userAccountRepository->update($user, [
            $mobileField => $mobile
        ]);

        return $this->updateMobileResponse($mobileField, $mobile);
    }

    public function validateMobile(string $mobileField, string $mobile) {
        $user = $this->userAccountRepository->getByUsername($mobileField, $mobile);
        if (!$user) return;

        if ($user->verified) $this->mobileAlreadyTaken();
        $user->forceDelete();
    }

    private function updateEmailResponse($emailField, $email)
    {
        return [
            $emailField => $email,
        ];
    }

    private function updateMobileResponse($mobileField, $mobile)
    {
        return [
            $mobileField => $mobile,
        ];
    }
}
