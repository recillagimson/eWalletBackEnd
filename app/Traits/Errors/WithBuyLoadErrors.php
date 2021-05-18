<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

trait WithBuyLoadErrors
{
    use WithErrors;

    private function prefixNotSupported()
    {
        $this->validationErrorMessage(ErrorCodes::buyLoadMobileNumberPrefixNotSupported,
            'Mobile number prefix is not supported.');
    }

    private function invalidMobileNumber()
    {
        $this->validationErrorMessage(ErrorCodes::buyLoadInvalidMobileNumber,
            'Mobile number is not supported.');
    }

    private function invalidProductCode()
    {
        $this->validationErrorMessage(ErrorCodes::buyLoadInvalidProductCode,
            'Product code is invalid.');
    }

    private function insufficientFunds()
    {
        $this->validationErrorMessage(ErrorCodes::buyLoadInsufficientFunds,
            'Squidpay account has no funds to topup.');
    }

    private function telcoUnavailable()
    {
        $this->validationErrorMessage(ErrorCodes::buyLoadTelcoUnavailable,
            'Telco is unvailable.');
    }

    private function productMismatch()
    {
        $this->validationErrorMessage(ErrorCodes::buyLoadProductMismatch,
            'Product mismatch.');
    }
}
