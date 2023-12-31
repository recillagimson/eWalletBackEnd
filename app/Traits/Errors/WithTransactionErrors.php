<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

trait WithTransactionErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION ERRORS
    |--------------------------------------------------------------------------
    */

    public function transactionInvalid()
    {
        $this->validationErrorMessage(ErrorCodes::transactionInvalid, 'Transaction type is invalid.');
    }

    public function transactionFailed()
    {
        $this->validationErrorMessage(ErrorCodes::transactionFailed, 'Transaction failed. Please try again.');
    }

    public function transactionNotFound()
    {
        $this->validationErrorMessage(ErrorCodes::transactionDoesntExists, 'Transaction does not exists.');
    }

    public function kycVerifyFailed(string $message) {
        $this->validationErrorMessage(ErrorCodes::verifyRequestFailed, $message);
    }
}
