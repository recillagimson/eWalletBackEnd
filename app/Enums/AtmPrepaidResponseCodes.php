<?php


namespace App\Enums;


class AtmPrepaidResponseCodes
{
    const requestReceived = 101;
    const invalidMobileNumber = 102;
    const invalidProductcode = 103;
    const invalidPartnerRefNo = 104;
    const invalidSignature = 105;
    const insufficientBalance = 106;
    const invalidCredentials = 107;
    const telcoUnavailable = 108;
    const internalError = 109;
    const serviceMaintenance = 110;
    const productMismatch = 111;
    const transactionSuccessful = 200;
    const transactionFailed = 201;
    const transactionQueued = 202;
}
