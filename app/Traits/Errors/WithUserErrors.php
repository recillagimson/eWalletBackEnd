<?php

namespace App\Traits\Errors;

use App\Enums\ErrorCodes;
use Error;

trait WithUserErrors
{
    use WithErrors;

    /*
    |--------------------------------------------------------------------------
    | USER VALIDATION ERRORS
    |--------------------------------------------------------------------------
    */

    public function userProfileNotUpdated()
    {
        $this->validationErrorMessage(ErrorCodes::userProfileNotUpdated,
            'User profile not updated.');
    }

    public function userInsufficientBalance()
    {
        $this->validationErrorMessage(ErrorCodes::userInsufficientBalance,
            'User has insufficient balance.');
    }

    public function userMonthlyLimitExceeded()
    {
        $this->validationErrorMessage(ErrorCodes::userMonthlyLimitExceeded,
            'Oh No! You have exceeded your monthly limit.');
    }

    public function handleCustomErrorMessage($key, $value)
    {
        $this->validationErrorMessage($key, $value);
    }

    public function userTierInvalid()
    {
        $this->validationErrorMessage(ErrorCodes::userTierInvalid,
            'Oops! To completely access all Squidpay services, please update your profile. Thank you.');
    }

    public function emailAlreadyTaken()
    {
        $this->validationErrorMessage(ErrorCodes::emailAlreadyTaken,
            'Oops! Email is already taken.');
    }

    public function mobileAlreadyTaken()
    {
        $this->validationErrorMessage(ErrorCodes::mobileAlreadyTaken,
            'Oops! Mobile Number is already taken.');
    }

    public function tierUpgradeAlreadyExist() 
    {
        $this->validationErrorMessage(ErrorCodes::tierUpgradeExist, 'Opps! You are not allowed to perform this transaction, there is a pending tier upgrade request.');
    }

    public function userAccountNotFound() {
        $this->validationErrorMessage(ErrorCodes::accountDoesNotExist, 'Account not found');
    }

    public function userSelfieNotFound() {
        $this->validationErrorMessage(ErrorCodes::userSelfieNotFound, 'Account Selfie not found');
    }

    public function bpiTokenInvalid() {
        $this->validationErrorMessage(ErrorCodes::bpiTokenInvalidOrExpired, 'Please Login to BPI');
    }

    public function recordNotFound() {
        $this->validationErrorMessage(ErrorCodes::kycRecordNotFound, 'KYC record not found');
    }

    public function accountCantBeUsed() {
        return $this->validationErrorMessage(ErrorCodes::bpiFundTopUp, 'Selected account cannot be used for this transaction');
    }

    public function bpiTransactionError(string $message) {
        return $this->validationErrorMessage(ErrorCodes::bpiTransactionError, $message);
    }

    public function bpiInvalidError(string $message) {
        return $this->validationErrorMessage(ErrorCodes::bpiInvalidError, $message);
    }

    public function dateFromBeforeDateCreated(string $dateCreated) {
        return $this->validationErrorMessage(ErrorCodes::dateFromBeforeDateCreated, 'Date From must be equal or greater than ' . $dateCreated);
    }

    public function referenceNumberNotFound() {
        return $this->validationErrorMessage(ErrorCodes::cebuanaReferenceNumberNotFound, 'Reference Number not found');
    }

    public function referenceNumberExpired() {
        return $this->validationErrorMessage(ErrorCodes::referenceNumberExpired, 'Reference Number expired');
    }

    public function lowerThanMinimumAmount() {
        return $this->validationErrorMessage(ErrorCodes::lowerThanMinimumAmount, 'Amount lower than minimum amount');
    }

    public function higherThanMaximumAmount() {
        return $this->validationErrorMessage(ErrorCodes::higherThanMaximumAmount, 'Amount higher than maximum amount');
    }
    
    public function dateToBeforeDateCreated(string $dateCreated) {
        return $this->validationErrorMessage(ErrorCodes::dateToBeforeDateCreated, 'Date To must be equal or greater than ' . $dateCreated);
    }

    public function dateFromBeforeDateToday(string $today) {
        return $this->validationErrorMessage(ErrorCodes::dateFromBeforeDateToday, 'Date From must be less than or equal ' . $today);
    }

    public function dateToBeforeDateToday(string $today) {
        return $this->validationErrorMessage(ErrorCodes::dateToBeforeDateToday, 'Date To must be less than or equal ' . $today);
    }

    public function updateProfileIdOrSelfieNotFound() {
        return $this->validationErrorMessage(ErrorCodes::updateProfileIdOrSelfieNotFound, 'Id or Selfie Photo not found');
    }
}
