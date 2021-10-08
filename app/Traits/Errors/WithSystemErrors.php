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
    public function verifyMerchantError()
    {
        $this->validationErrorMessage(ErrorCodes::verifyMerchant,
            'Verify Merchant Error.');
    }
    public function showDocumentError()
    {
        $this->validationErrorMessage(ErrorCodes::showDocument,
            'Merchant Show Document Error.');
    }
    public function updateDocumentStatusError()
    {
        $this->validationErrorMessage(ErrorCodes::updateDocumentStatus,
            'Merchant Update Document Error.');
    }
}
