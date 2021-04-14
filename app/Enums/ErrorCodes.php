<?php


namespace App\Enums;


class ErrorCodes
{
    //AUTHENTICATION ERRORS - 100
    const LoginFailed = 101;
    const UnverifiedAccount = 102;
    const AccountDoesNotExist = 103;
    const InvalidClient = 104;
    const AccountLockedOut = 105;
    const PasswordUsed = 106;
    const PasswordNotAged = 107;
    const OTPInvalid = 108;
    const OTPExpired = 109;
    const OTPMaxedAttempts = 110;

    //ENCRYPTION ERRORS - 150
    const PayloadInvalid = 151;

}
