<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

trait WithPayBillsErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | PAY BILLS VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */



    private function insuficientBalance()
    {
        $this->validationErrorMessage(ErrorCodes::userInsufficientBalance, 'Not Enough balance.');
    }

    private function invalidUser()
    {
        $this->validationErrorMessage(ErrorCodes::accountDoesNotExist, 'Account does not exists.');
    }

    private function errorEncountered()
    {
        $this->validationErrorMessage(ErrorCodes::transactionErrorEncountered, 'Transaction error encountered.');
    }

    private function invalidDigitsLength(string $digits)
    {
        $this->validationErrorMessage(ErrorCodes::invalidDigitsLength, 'Account number must be ' . $digits . ' digits.');
    }

    private function noAmountProvided()
    {
        $this->validationErrorMessage(ErrorCodes::noAmountProvided, 'Please provide the amount.');
    }

    private function requiredField(string $field, string $code)
    {
        $this->validationErrorMessage(ErrorCodes::requiredField,  $code.' : '.' Please provide the ' . $field . '.');
    }

}
