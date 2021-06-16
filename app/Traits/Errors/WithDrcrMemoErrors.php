<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

trait WithDrcrMemoErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | DRCR MEMO VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */



    private function insuficientBalance()
    {
        $this->validationErrorMessage(ErrorCodes::userInsufficientBalance, 'Not Enough balance.');
    }
}
