<?php


namespace App\Traits\Errors;


use App\Enums\ErrorCodes;
use App\Enums\PayBillsConfig;
use Illuminate\Validation\ValidationException;

trait WithPayBillsErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | PAY BILLS VALIDATION EXCEPTION HELPER METHODS
    |--------------------------------------------------------------------------
    */


    private function invalidErrorCode()
    {
        $this->validationErrorMessage(ErrorCodes::invalidErrorCode, 'Invalid error code');
    }

    private function insuficientBalance()
    {
        $this->validationErrorMessage(ErrorCodes::userInsufficientBalance, 'Not Enough balance.');
    }

    private function payBillsNotEnoughBalance($otherCharges, $serviceFee)
    {
        $this->validationErrorMessageWithAmount(ErrorCodes::payBillsNotEnoughBalance, 'Not Enough balance. This service has PHP '. number_format($serviceFee,2).' service fee and PHP '. number_format($otherCharges,2) .' other charges fee.');
    }

    
    private function invalidUser()
    {
        $this->validationErrorMessage(ErrorCodes::accountDoesNotExist, 'Account does not exists.');
    }

    private function errorEncountered()
    {
        $this->validationErrorMessage(ErrorCodes::transactionErrorEncountered, 'Transaction error encountered.');
    }

    public function accountWithDFO($providerArrayResponse, $serviceFee, $otherCharges)
    {
        return $this->validationAccountWithDFO(ErrorCodes::accountWithDFO, 'Provider Error.', $providerArrayResponse, $serviceFee, $otherCharges);
    }

    private function disconnectedAccount($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::disconnectedAccount, $errorMessage);
    }

    private function invalidParameter($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidParameter, $errorMessage);
    }

    private function parameterMissing($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::parameterMissing, $errorMessage);
    }

    private function invalidAccountNumberFormat($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidAccountNumberFormat, $errorMessage);
    }
    
    private function insufficientAmount($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::insufficientAmount, $errorMessage);
    }

    private function maximumAmountExceeded($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::maximumAmountExceeded, $errorMessage);
    }

    private function invalidNumericFormat($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidNumericFormat, $errorMessage);
    }

    private function invalidAlphaDashFormat($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidAlphaDashFormat, $errorMessage);
    }

    private function invalidSelectedValue($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidSelectedValue, $errorMessage);
    }

    private function clientReferenceAlreadyExists($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::clientReferenceAlreadyExists, $errorMessage);
    }

    private function callBackUrlIsInvalid($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::callBackUrlIsInvalid, $errorMessage);
    }

    private function transactionFrequencyLimitExceeded($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::transactionFrequencyLimitExceeded, $errorMessage);
    }

    private function invalidOtherCharges($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidOtherCharges, $errorMessage);
    }

    private function invalidDateFormat($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidDateFormat, $errorMessage);
    }
    private function invalidServiceFeeValue($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidServiceFeeValue, $errorMessage);
    }

    private function walletBalanceBelowThreshold($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::walletBalanceBelowThreshold, $errorMessage);
    }

    private function invalidAlphaNumericFormat($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidAlphaNumericFormat, $errorMessage);
    }

    private function valueShouldBeSameAsValueOfX($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::valueShouldBeSameAsValueOfX, $errorMessage);
    }

    private function accountNumberDidNotPassCheckDigitValidation($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::accountNumberDidNotPassCheckDigitValidation, $errorMessage);
    }

    private function invalidAmount($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::invalidAmount, $errorMessage);
    }

    private function accountNumberAlreadyExpired($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::accountNumberAlreadyExpired, $errorMessage);
    }

    private function transactionAlreadyBeenPaid($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::transactionAlreadyBeenPaid, $errorMessage);
    }

    private function amountIsAboveWalletLimit($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::amountIsAboveWalletLimit, $errorMessage);
    }

    private function theOtherChargesMustbePhp($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::theOtherChargesMustbePhp, $errorMessage);
    }

    private function randomError($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::randomError, $errorMessage);
    }

    private function theAccountNumberisNotSupportedByTheBank($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::theAccountNumberisNotSupportedByTheBank, $errorMessage);
    }

    private function theAccountNumberMustStartWithAnyOf($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::theAccountNumberMustStartWithAnyOf, $errorMessage);
    }

    private function possibleDuplicateDetected($errorMessage)
    {
        $this->validationErrorMessage(ErrorCodes::possibleDuplicateDetected, $errorMessage);
    }

    private function invalidAccountNumber()
    {
        $this->validationErrorMessage(ErrorCodes::invalidAccountNumber, 'Invalid Account Number.');
    }

    private function overDueBills()
    {
        $this->validationErrorMessage(ErrorCodes::overDue, "This biller does not accept overdue bill. Please pay directly to the biller.");
    }

    private function correctAccountName()
    {
        $this->validationErrorMessage(ErrorCodes::correctAccountName, "Please provide the correct Account Name.");
    }

    private function lettersSpaces()
    {
        $this->validationErrorMessage(ErrorCodes::lettersSpaces, "The account name only accepts letters and spaces.");
    }

    private function correctAmountDue()
    {
        $this->validationErrorMessage(ErrorCodes::correctAmountDue, "Please provide the correct Amount Due.");
    }

    private function correctDueDate()
    {
        $this->validationErrorMessage(ErrorCodes::correctDueDate, "Please provide the correct Due Date and amount.");
    }

    private function correctRegion()
    {
        $this->validationErrorMessage(ErrorCodes::correctRegion, "Please provide the correct Region.");
    }


    private function endpointRequestTimeOut()
    {
        $this->validationErrorMessage(ErrorCodes::endpointRequestTimeOut, PayBillsConfig::endpointRequestTimeOut);
    }
    
}
