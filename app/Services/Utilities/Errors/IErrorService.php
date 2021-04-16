<?php

namespace App\Services\Utilities\Errors;

interface IErrorService
{
    /*
    |--------------------------------------------------------------------------
    | AUTH VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */

    public function loginFailed();
    public function accountUnverified();
    public function accountDoesntExist();
    public function invalidCredentials();
    public function accountLockedOut();
    public function passwordUsed();
    public function passwordNotAged(int $minPasswordAge);

    public function otpInvalid(string $message = 'OTP is invalid.');
    public function otpTypeInvalid();
    public function otpIsExpired();
    public function otpMaxedAttempts();

    /*
    |--------------------------------------------------------------------------
    | 3RD PARTY APIS ERRORS
    |--------------------------------------------------------------------------
    */

    public function tpaFailedAuthentication(string $provider);
}
