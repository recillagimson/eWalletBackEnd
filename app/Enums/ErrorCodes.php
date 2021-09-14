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
    const otpMaxedGenerationAttempt = 115;


    //ENCRYPTION ERRORS - 150
    const payloadInvalid = 151;

    //3RD PARTY APIS - 200
    const tpaFailedAuthentication = 201;
    const tpaErrorOccured = 202;
    const tpaInvalidProvider = 203;
    const tpaErrorCatch = 204;

    //TRANSACTIONS - 300
    const transactionInvalid = 301;
    const transactionFailed = 302;
    const transactionDoesntExists = 303;
    const transactionErrorEncountered = 304;

    //USER ERRORS - 400
    const userProfileNotUpdated = 401;
    const userInsufficientBalance = 402;
    const userInvalidQR = 403;
    const userDetailsNotFound = 404;
    const userMonthlyLimitExceeded = 405;
    const userTierInvalid = 406;
    const emailAlreadyTaken = 407;
    const mobileAlreadyTaken = 408;
    const receiverMonthlyLimitExceeded = 409;
    const tierUpgradeExist = 410;
    const userSelfieNotFound = 411;
    const bpiTokenInvalidOrExpired = 412;
    const invalidStatus = 413;
    const userAccountNotFound = 414;
    const referenceNumberNotFound = 415;
    const invalidTypeOfMemo = 416;
    const isEmpty = 417;
    const isExisting = 418;
    const kycRecordNotFound = 419;

    //BUY LOAD - 500
    const buyLoadMobileNumberPrefixNotSupported = 501;
    const buyLoadInvalidMobileNumber = 502;
    const buyLoadInvalidProductCode = 503;
    const buyLoadInsufficientFunds = 504;
    const buyLoadTelcoUnavailable = 505;
    const buyLoadProductMismatch = 506;

    // eKYC - 600
    const ocrMismatch = 601;
    const verifyRequestFailed = 602;

    // Bayad Center 700
    const accountWithDFO = 701;
    const disconnectedAccount = 702;
    const invalidParameter = 703;
    const parameterMissing = 704;
    const invalidAccountNumberFormat = 705;
    const insufficientAmount = 706;
    const maximumAmountExceeded = 707; 
    const invalidNumericFormat = 708;
    const invalidAlphaDashFormat = 709; 
    const invalidSelectedValue = 710;
    const clientReferenceAlreadyExists = 711;
    const callBackUrlIsInvalid = 712;
    const transactionFrequencyLimitExceeded = 713;
    const invalidOtherCharges = 714;
    const invalidDateFormat = 715;
    const invalidServiceFeeValue = 716;
    const walletBalanceBelowThreshold = 717;
    const invalidAlphaNumericFormat = 718;
    const valueShouldBeSameAsValueOfX = 719;
    const accountNumberDidNotPassCheckDigitValidation = 720;
    const invalidAmount = 721;
    const accountNumberAlreadyExpired = 722;
    const transactionAlreadyBeenPaid = 723;
    const amountIsAboveWalletLimit = 724;
    const theOtherChargesMustbePhp = 725;
    const theAccountNumberisNotSupportedByTheBank = 727;
    const theAccountNumberMustStartWithAnyOf = 728;
    const possibleDuplicateDetected  = 730;
    const invalidErrorCode = 731;

}
