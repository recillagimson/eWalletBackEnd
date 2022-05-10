<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

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

    public function tpaInvalidProvider()
    {
        $this->validationErrorMessage(ErrorCodes::tpaInvalidProvider, 'Invalid Provider.');
    }

    public function tpaErrorCatch($providerArrayResponse)
    {
        return $this->validationCatchErrorMessage(ErrorCodes::tpaErrorCatch, 'Provider Error.', $providerArrayResponse);
    }

    public function tpaErrorCatchMeralco($providerArrayResponse, $serviceFee, $otherCharges)
    {
        return $this->validationCatchErrorMessageMeralco(ErrorCodes::tpaErrorCatch, 'Provider Error.', $providerArrayResponse, $serviceFee, $otherCharges);
    }


}
