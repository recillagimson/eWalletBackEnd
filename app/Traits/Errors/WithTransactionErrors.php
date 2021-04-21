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

    public function transInvalid()
    {
        $this->validationErrorMessage(ErrorCodes::transactionInvalid, 'Transaction type is invalid.');
    }

    public function transFailed()
    {
        $this->validationErrorMessage(ErrorCodes::transactionFailed, 'Transaction failed. Please try again.');
    }
}
