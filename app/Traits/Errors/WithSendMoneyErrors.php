<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;
use Illuminate\Validation\ValidationException;

trait WithSendMoneyErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | SEND MONEY VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */


    private function invalidAccount()
    {
        $this->validationErrorMessage(ErrorCodes::accountDoesNotExist, 'Account does not exists.');
    }

    private function invalidRecipient()
    {
        $this->validationErrorMessage(ErrorCodes::transactionInvalid, 'Not allowed to send to your own account.');
    }

    private function insuficientBalance()
    {
        $this->validationErrorMessage(ErrorCodes::userInsufficientBalance, 'Not Enough balance.');
    }

    private function invalidQr()
    {
        $this->validationErrorMessage(ErrorCodes::userInvalidQR, 'Qr transaction does not exists.');
    }

    private function userDetailsNotFound()
    {
        $this->validationErrorMessage(ErrorCodes::userDetailsNotFound, 'User details not found. Please update your profile.');
    }


}
