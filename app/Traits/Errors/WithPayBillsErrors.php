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


}
