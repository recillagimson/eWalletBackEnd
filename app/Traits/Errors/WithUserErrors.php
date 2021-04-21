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
        $this->validationErrorMessage(ErrorCodes::userProfileNotUpdated, 'User profile not updated.');
    }

    public function userInsufficientBalance()
    {
        $this->validationErrorMessage(ErrorCodes::userInsufficientBalance, 'User has insufficient balance.');
    }
}
