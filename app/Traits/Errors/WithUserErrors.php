<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

trait WithUserErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | USER VALIDATION ERRORS
    |--------------------------------------------------------------------------
    */

    public function userProfileNotUpdated()
    {
        $this->validationErrorMessage(ErrorCodes::userProfileNotUpdated,
            'User profile not updated.');
    }

    public function userInsufficientBalance()
    {
        $this->validationErrorMessage(ErrorCodes::userInsufficientBalance,
            'User has insufficient balance.');
    }

    public function userMonthlyLimitExceeded()
    {
        $this->validationErrorMessage(ErrorCodes::userMonthlyLimitExceeded,
            'Oh No! You have exceeded your monthly limit.');
    }

    public function userTierInvalid()
    {
        $this->validationErrorMessage(ErrorCodes::userTierInvalid,
            'Oops! To completely access all Squidpay services, please update your profile. Thank you.');
    }

    public function emailAlreadyTaken()
    {
        $this->validationErrorMessage(ErrorCodes::emailAlreadyTaken,
            'Oops! Email is already taken.');
    }
}
