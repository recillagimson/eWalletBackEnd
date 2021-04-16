<?php


namespace App\Services\Utilities\Errors;


use App\Enums\ErrorCodes;
use Illuminate\Validation\ValidationException;


class ErrorService implements IErrorService
{
    /*
    |--------------------------------------------------------------------------
    | AUTH VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */

    public function loginFailed()
    {
        $this->validationErrorMessage(ErrorCodes::loginFailed, 'Login Failed');
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
        $this->validationErrorMessage(ErrorCodes::accountLockedOut, 'Account has been locked out. Due to 3 failed login attempts.');
    }

    public function passwordUsed()
    {
        $this->validationErrorMessage(ErrorCodes::passwordUsed,'Password has already been used.');
    }

    public function passwordNotAged(int $minPasswordAge)
    {
        $this->validationErrorMessage(ErrorCodes::passwordNotAged, 'Password cannot be changed for at least '.$minPasswordAge.' day/s.');
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
        $this->validationErrorMessage(ErrorCodes::otpMaxedAttempts, 'Reached the maximum allowed attempts.');
    }

    /*
    |--------------------------------------------------------------------------
    | 3RD PARTY APIS ERRORS
    |--------------------------------------------------------------------------
    */

    public function tpaFailedAuthentication(string $provider)
    {
        $this->validationErrorMessage(ErrorCodes::tpaFailedAuthentication, 'TPA:'.$provider.' Authentication Failed.');
    }




    private function validationErrorMessage($errorCode, $errorMessage)
    {
        throw ValidationException::withMessages([
           'error_code' => $errorCode,
           'message' => $errorMessage
        ]);
    }


}
