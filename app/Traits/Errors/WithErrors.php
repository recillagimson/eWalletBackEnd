<?php


namespace App\Traits\Errors;


use Illuminate\Validation\ValidationException;

trait WithErrors
{
    private function validationErrorMessage($errorCode, $errorMessage)
    {
        throw ValidationException::withMessages([
            'error_code' => $errorCode,
            'message' => $errorMessage
        ]);
    }

    private function validationError($field, $errorMessage)
    {
        throw ValidationException::withMessages([
            $field => $errorMessage
        ]);
    }
}
