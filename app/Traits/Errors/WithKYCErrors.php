<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;

trait WithKYCErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | USER VALIDATION ERRORS
    |--------------------------------------------------------------------------
    */

    public function OCRmatchOCR()
    {
        $this->validationErrorMessage(ErrorCodes::ocrMismatch,
            'OCR matching failed.');
    }
}
