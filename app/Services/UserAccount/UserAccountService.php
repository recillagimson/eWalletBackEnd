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

    private function updateEmailResponse($emailField, $email)
    {
        return [
            $emailField => $email,
        ];
    }
}
