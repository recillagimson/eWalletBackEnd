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

    private function invalidStatus1()
    {
        $this->validationErrorMessage(ErrorCodes::invalidStatus, 'Invalid status: acceptable status are P => Pending, A => Approve, D => Decline');
    }

    private function isEmpty()
    {
        $this->validationErrorMessage(ErrorCodes::isEmpty, 'Add remarks if status is Decline');
    }

    private function isExisting()
    {
        $this->validationErrorMessage(ErrorCodes::isExisting, 'Already gone approval, cannot proceed again');
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
        $this->validationErrorMessage(ErrorCodes::invalidTypeOfMemo, 'Invalid Type Of Memo');
    }

    private function controlNumberAlreadyUploaded()
    {
        $this->validationErrorMessage(ErrorCodes::controlNumberAlreadyUploaded, 'Control Number Already Uploaded');
    }
}
