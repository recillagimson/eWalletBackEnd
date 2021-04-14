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

    const pinCodeUpdated = 'Account Pin Code has been updated.';
}
