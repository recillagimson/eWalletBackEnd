<?php


namespace App\Enums;


class ErrorCodes
{
    //AUTHENTICATION ERRORS - 100
    const loginFailed = 101;
    const unverifiedAccount = 102;
    const accountDoesNotExist = 103;
    const invalidClient = 104;
    const accountLockedOut = 105;
    const passwordUsed = 106;
    const passwordNotAged = 107;
    const otpInvalid = 108;
    const otpExpired = 109;
    const otpMaxedAttempts = 110;
    const otpTypeInvalid = 111;

    //ENCRYPTION ERRORS - 150
    const payloadInvalid = 151;

    //3RD PARTY APIS - 200
    const tpaFailedAuthentication = 201;

}
