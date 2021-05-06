<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

trait WithAuthErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | AUTH VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */

    public function loginFailed()
    {
        $this->validationErrorMessage(ErrorCodes::confirmationFailed, 'Login Failed');
    }

    public function confirmationFailed()
    {
        $this->validationErrorMessage(ErrorCodes::confirmationFailed,
            'Transaction Confirmation Failed');
    }

    public function accountUnverified()
    {
        $this->validationErrorMessage(ErrorCodes::unverifiedAccount, 'Unverified Account.');
    }

    public function accountDoesntExist()
    {
        $this->validationErrorMessage(ErrorCodes::accountDoesNotExist, 'Account does not exists.');
    }

    public function invalidCredentials()
    {
        $this->validationErrorMessage(ErrorCodes::invalidClient, 'Invalid client credentials.');
    }

    public function accountLockedOut()
    {
        $this->validationErrorMessage(ErrorCodes::accountLockedOut,
            'Your Account has been locked, Please contact Squidpay Support for assistance
            in unlocking your account.');
    }

    public function passwordUsed()
    {
        $this->validationErrorMessage(ErrorCodes::passwordUsed,
            'Password / Pin has already been used.');
    }

    public function passwordNotAged(int $minPasswordAge)
    {
        $this->validationErrorMessage(ErrorCodes::passwordNotAged,
            'Password / Pin cannot be changed for at least ' . $minPasswordAge . ' day/s.');
    }

    public function otpTypeInvalid() {
        $this->validationErrorMessage(ErrorCodes::otpTypeInvalid, 'OTP Type is invalid');
    }

    public function otpInvalid(string $message = 'OTP is invalid.')
    {
        $this->validationErrorMessage(ErrorCodes::otpInvalid, $message);
    }

    public function otpIsExpired()
    {
        $this->validationErrorMessage(ErrorCodes::otpExpired, 'OTP is expired.');
    }

    public function otpMaxedAttempts()
    {
        $this->validationErrorMessage(ErrorCodes::otpMaxedAttempts,
            'Reached the maximum allowed attempts.');
    }

    public function accountAlreadyTaken()
    {
        $this->validationErrorMessage(ErrorCodes::accountAlreadyTaken,
            'Email / Mobile Number is already taken.');
    }

    public function accountDeactivated()
    {
        $this->validationErrorMessage(ErrorCodes::accountDeactivated,
            'Your account is disabled. Please contact Squidpay support.');
    }
}
