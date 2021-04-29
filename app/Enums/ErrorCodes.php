<?php


namespace App\Enums;


class ErrorCodes
{
    //AUTHENTICATION / USER ERRORS - 100
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
    const accountAlreadyTaken = 112;
    const confirmationFailed = 113;
    const accountDeactivated = 114;

    //ENCRYPTION ERRORS - 150
    const payloadInvalid = 151;

    //3RD PARTY APIS - 200
    const tpaFailedAuthentication = 201;
    const tpaErrorOccured = 202;

    //TRANSACTIONS - 300
    const transactionInvalid = 301;
    const transactionFailed = 302;

    //USER ERRORS - 400
    const userProfileNotUpdated = 401;
    const userInsufficientBalance = 402;
    const userInvalidQR = 403;
    const userDetailsNotFound = 404;
    const userMonthlyLimitExceeded = 405;
    const userTierInvalid = 406;


}
