<?php


namespace App\Enums;


class SuccessMessages
{
    const loginSuccessful = 'Login successful';
    const loginValidationPassed = 'Login validation passed.';
    const loginVerificationSuccessful = 'Login verification successful';

    const accountRegistered = 'Account registration successful.';
    const accountValidationPassed = 'Account validation passed.';
    const accountVerification = 'Account verification successful.';

    const passwordRecoveryRequestSuccessful = 'Password recovery request successful';
    const passwordRecoveryVerificationSuccessful = 'Password recovery verification successful';
    const passwordUpdateSuccessful = 'Password has been updated';

    const otpSent = 'OTP has been sent.';

    const pinCodeUpdated = 'Account Pin Code has been updated.';

    // Send Money Controller
    const sendMoneySuccessFul = 'Send money Successful';
    const scanQrSuccessful = 'Scan Qr Successful';
    const generateQrSuccessful = 'Generate Qr Successful';

    const success = 'Success';
    const recordSaved = 'Record has been saved.';
    const recordDeleted = 'Record has been deleted';
}
