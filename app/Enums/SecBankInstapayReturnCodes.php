<?php


namespace App\Enums;


class SecBankInstapayReturnCodes
{
    const success = '0';
    const invalidTransaction = '12';
    const invalidAmount = '13';
    const invalidBank = '15';
    const systemError = '19';
    const unsupportedTransaction = '24';
    const accountDoesntexist = '25';
    const pinTriesExceeded = '38';
    const noCreditAccount = '39';
    const insufficientBalance = '51';
    const invalidCurrentAccount = '52';
    const invalidSavingsAccount = '53';
    const expiredCard = '54';
    const incorrectPIN = '55';
    const noCardRecord = '56';
    const invalidTerminalID = '58';
    const amountLimitExceeded = '61';
    const accountClosed = '62';
    const transactionLimit = '64';
}
