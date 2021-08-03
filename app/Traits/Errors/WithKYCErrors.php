<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;
use Illuminate\Validation\ValidationException;

trait WithKYCErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | USER VALIDATION ERRORS
    |--------------------------------------------------------------------------
    */

    public function OCRmatchOCR() {
        throw ValidationException::withMessages([
            ErrorCodes::ocrMismatch => 'OCR matching failed'
        ]);
    }
}
