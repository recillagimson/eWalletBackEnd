<?php


namespace App\Enums;


class UbpResponseCodes
{
    const receivedRequest = 'RT';
    const successfulTransaction = 'TS';
    const failedTransaction = 'TF';
    const processing = 'SP';
    const networkIssue = 'NC';
    const forConfirmation = 'SC';
}
