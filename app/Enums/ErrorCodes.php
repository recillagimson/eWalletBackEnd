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
    const InvalidOTP = 106;
    const PasswordUsed = 107;
    const PasswordNotAged = 108;

    //ENCRYPTION ERRORS - 150
    const PayloadInvalid = 151;

}
