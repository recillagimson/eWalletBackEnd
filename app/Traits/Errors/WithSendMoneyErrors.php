<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

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
        $this->validationErrorMessage(ErrorCodes::accountDoesNotExist, 'Account does not exist.');
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

    private function recipientDetailsNotFound()
    {
        $this->validationErrorMessage(ErrorCodes::userDetailsNotFound, 'Recipient details not found');
    }

    private function senderDetailsNotFound()
    {
        $this->validationErrorMessage(ErrorCodes::userDetailsNotFound, 'Sender details not found');
    }


}
