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
    const accountAlreadyVerified = 116;


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
    const bpiFundTopUp = 424;
    const bpiTransactionError = 425;
    const bpiInvalidError = 426;
    const controlNumberAlreadyUploaded = 427;
    const updateProfileIdOrSelfieNotFound = 428;


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
    const randomError = 726;
    const theAccountNumberisNotSupportedByTheBank = 727;
    const theAccountNumberMustStartWithAnyOf = 728;
    const possibleDuplicateDetected = 730;
    const invalidErrorCode = 731;
    const invalidAccountNumber = 732;
    const endpointRequestTimeOut = 734;
    const payBillsNotEnoughBalance = 735;
    const overDue = 736;
    const correctAccountName = 737;
    const lettersSpaces = 738;
    const correctAmountDue = 739;
    const correctDueDate = 740;
    const correctRegion = 741;

    // Merchant 800
    const merchantClientToken = 801;
    const merchantRequestId = 802;
    const merchantDecryption = 803;
    const merchantList = 804;
    const verifyMerchant = 805;
    const showDocument = 806;
    const updateDocumentStatus = 807;

    //UBP Errors
    const ubpNoAccountLinked = 901;
    const ubpAccountLinkedExpired = 902;

    // 1000
    const dateFromBeforeDateCreated = 1000;

    // CEBUANA
    const cebuanaReferenceNumberNotFound = 1100;
    const referenceNumberExpired = 1101;
    const lowerThanMinimumAmount = 1102;
    const higherThanMaximumAmount = 1103;


    const dateToBeforeDateCreated = 1001;
    const dateFromBeforeDateToday = 1002;
    const dateToBeforeDateToday = 1003;
}
