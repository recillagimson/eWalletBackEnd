<?php

namespace App\Traits\Errors;

use App\Enums\ErrorCodes;

trait WithSystemErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | USER VALIDATION ERRORS
    |--------------------------------------------------------------------------
    */

    public function clientTokenError()
    {
        $this->validationErrorMessage(ErrorCodes::merchantClientToken,
            'Merchant Access Token Error.');
    }

    public function requestIdError()
    {
        $this->validationErrorMessage(ErrorCodes::merchantRequestId,
            'Merchant Request ID Error.');
    }

    public function decryptionError()
    {
        $this->validationErrorMessage(ErrorCodes::merchantDecryption,
            'Merchant Decryption Error.');
    }
    public function getMerchantListError()
    {
        $this->validationErrorMessage(ErrorCodes::merchantList,
            'Get Merchant List Error.');
    }
}
