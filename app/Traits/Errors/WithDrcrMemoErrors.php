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


    private function userAccountNotFound()
    {
        $this->validationErrorMessage(ErrorCodes::userAccountNotFound, 'User Account Not Found');
    }

    private function referenceNumberNotFound()
    {
        $this->validationErrorMessage(ErrorCodes::referenceNumberNotFound, 'Reference Number Not Found');
    }

    private function invalidTypeOfMemo()
    {
        $this->validationErrorMessage(ErrorCodes::referenceNumberNotFound, 'Invalid Type Of Memo');
    }


}
