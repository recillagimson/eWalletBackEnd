<?php


namespace App\Enums;


class SuccessMessages
{
    const loginSuccessful = 'Login successful';
    const loginValidationPassed = 'Login validation passed.';
    const loginVerificationSuccessful = 'Login verification successful';
    const confirmationSuccessful = 'Transaction confirmation successful';

    const accountRegistered = 'Account registration successful.';
    const accountValidationPassed = 'Account validation passed.';
    const accountVerification = 'Account verification successful.';

    const passwordRecoveryRequestSuccessful = 'Password / Pin recovery request successful';
    const passwordRecoveryVerificationSuccessful = 'Password / Pin recovery verification successful';
    const passwordChangeVerificationSuccessful = 'Password / Pin change verification successful';
    const passwordUpdateSuccessful = 'Password / Pin has been updated';
    const passwordValidationPassed = 'Password / Pin validation passed.';

    const otpSent = 'OTP has been sent.';
    const otpVerificationSuccessful = 'OTP verification successful.';

    const pinCodeUpdated = 'Account Pin Code has been updated.';

    // Send Money Controller
    const sendMoneySuccessFul = 'Send money successful';
    const validateSendMoney = 'Send money validation successful';
    const scanQrSuccessful = 'Scan Qr successful';
    const generateQrSuccessful = 'Generate Qr successful';

    const success = 'Success';
    const recordSaved = 'Record has been saved.';
    const recordDeleted = 'Record has been deleted';

    // Add Money Controller
    const URLGenerated = 'Generate URL Successful';
    const addMoneySuccess = 'Added money Successfully';
    const addMoneyFailed = 'Add money Failed';
    const addMoneyPending = 'Waiting for deposit to selected bank';
    const addMoneyCancel = 'Add money request Cancelled';
    const addMoneyStatusAcquired = 'Transaction status acquired Successfully';
}
