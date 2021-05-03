<?php


namespace App\Enums;


class UbpResponseStates
{
    const receivedRequest = 'Received Transaction Request';
    const creditedToAccount = 'Credited Beneficiary Account';
    const failedToCreditAccount = 'Failed to Credit Beneficiary Account';
    const sentForProcessing = 'Sent for Processing';
    const networkIssue = 'Network Issue - Core';
    const forConfirmation = 'Sent for Confirmation';
}
