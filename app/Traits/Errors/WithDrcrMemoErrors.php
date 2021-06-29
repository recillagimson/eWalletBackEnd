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


    private function invalidStatus()
    {
        $this->validationErrorMessage(ErrorCodes::invalidStatus, 'Invalid status: acceptable status are P => Pending, A => Approve, D => Decline, ALL => All');
    }
}
