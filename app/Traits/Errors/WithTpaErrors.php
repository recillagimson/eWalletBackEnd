<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

trait WithTpaErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | 3RD PARTY APIS ERRORS
    |--------------------------------------------------------------------------
    */

    public function tpaFailedAuthentication(string $provider)
    {
        $this->validationErrorMessage(ErrorCodes::tpaFailedAuthentication, 'TPA:' . $provider . ' Authentication Failed.');
    }

    public function tpaErrorOccured(string $provider)
    {
        $this->validationErrorMessage(ErrorCodes::tpaErrorOccured, 'TPA:' . $provider . ' Error Occured. Please Try Again.');
    }
}
