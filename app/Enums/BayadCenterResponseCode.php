<?php


namespace App\Enums;


class BayadCenterResponseCode
{
    const pending = 'PENDING';
    const onhold = 'ONHOLD';
    const queued = 'QUEUED';
    const processing = 'PROCESSING';
    const billerValidationFailed = 'BILLER_VALIDATION_FAILED';
    const billerTimeOut = 'BILLER_TIMEOUT';
    const paymentPosted = 'PAYMENT_POSTED';
}
