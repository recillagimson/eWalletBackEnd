<?php


namespace App\Traits\Errors;

use Illuminate\Http\Response;
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

    private function validationErrorMessageWithAmount($errorCode, $errorMessage)
    {
        throw ValidationException::withMessages([
            'error_code' => $errorCode,
            'amount' => $errorMessage
        ]);
    }

    private function validationError($field, $errorMessage)
    {
        throw ValidationException::withMessages([
            $field => $errorMessage
        ]);
    }

    // requested format for FE team
    private function validationCatchErrorMessage($errorCode, $errorMessage, $providerArrayResponse)
    {
        $withMessages =  array('message' => 'The given data was invalid.');
        $withError  =  array('errors' => array('error_code' => [$errorCode], 'message' => [$errorMessage]));
        $providerError = array('provider_error' => array($providerArrayResponse));

        return array_merge($withMessages , $withError, $providerError);
    }

    // requested format for FE team
    private function validationAccountWithDFO($errorCode, $errorMessage, $providerArrayResponse, $serviceFee, $otherCharges): array
    {
        $withMessages =  array('message' => 'The given data was invalid.');
        $withError  =  array('errors' => array('error_code' => [$errorCode], 'message' => [$errorMessage]));
        $feeAndCharges = array_merge(array('serviceFee' => $serviceFee), array('otherCharges' => $otherCharges));
        $providerArrayResponse = array_merge($providerArrayResponse['data'], $feeAndCharges);
        $withData = array('data' => $providerArrayResponse);
        $providerError = array('provider_error' => [$withData]);

        return array_merge($withMessages, $withError, $providerError);
    }

    // requested format for FE team
    private function validationCatchErrorMessageMeralco($errorCode, $errorMessage, $providerArrayResponse, $serviceFee, $otherCharges): array
    {
        $withMessages =  array('message' => 'The given data was invalid.');
        $withError  =  array('errors' => array('error_code' => [$errorCode], 'message' => [$errorMessage]));
        $feeAndCharges = array_merge(array('serviceFee' => $serviceFee), array('otherCharges' => $otherCharges));
        $providerArrayResponse = array_merge($providerArrayResponse['data'], $feeAndCharges);
        $withData = array('data' => $providerArrayResponse);
        $providerError = array('provider_error' => [$withData]);

        return array_merge($withMessages, $withError, $providerError);
    }
    
}
