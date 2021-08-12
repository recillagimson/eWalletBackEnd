<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

trait WithDisbursementErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | Disvursement DBP VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */



    private function insuficientBalance()
    {
        $this->validationErrorMessage(ErrorCodes::userInsufficientBalance, 'Not Enough balance.');
    }


}