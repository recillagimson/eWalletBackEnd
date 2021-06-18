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

    public function handleCustomErrorMessage($key, $value)
    {
        $this->validationErrorMessage($key, $value);
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

    public function mobileAlreadyTaken()
    {
        $this->validationErrorMessage(ErrorCodes::mobileAlreadyTaken,
            'Oops! Mobile Number is already taken.');
    }

    public function tierUpgradeAlreadyExist() 
    {
        $this->validationErrorMessage(ErrorCodes::tierUpgradeExist, 'Opps! You are not allowed to perform this transaction, there is a pending tier upgrade request.');
    }

    public function userAccountNotFound() {
        $this->validationErrorMessage(ErrorCodes::accountDoesNotExist, 'Account not found');
    }
}
