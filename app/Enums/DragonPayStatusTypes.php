<?php

namespace App\Enums;

class DragonPayStatusTypes
{
    const Success = 'S';
    const Failure = 'F';
    const Pending = 'P';
    const Unknown = 'U';
    const Refund = 'R';
    const Chargeback = 'K';
    const Void = 'V';
    const Authorized = 'A';

    const requestSuccessful = 'S';
    const requestFailed = 'F';

    // const Success = ['S', 'SUCCESS'];
    // const Failure = ['F', 'FAILURE'];
    // const Pending = ['P', 'PENDING'];
    // const Unknown = ['U', 'UNKNOWN'];
    // const Refund = ['R','REFUND'];
    // const Chargeback = ['K', 'CHARGEBACK'];
    // const Void = ['V', 'VOID'];
    // const Authorized = ['A', 'AUTHRORIZED'];
}
