<?php

namespace App\Enums;

class DragonPayStatusTypes
{
    const Success = 'SUCCESS';
    const Failure = 'FAILURE';
    const Pending = 'PENDING';
    const Unknown = 'UNKNOWN';
    const Refund = 'REFUND';
    const Chargeback = 'CHARGEBACK';
    const Void = 'VOID';
    const Authorized = 'AUTHRORIZED';

    // const Success = ['S', 'SUCCESS'];
    // const Failure = ['F', 'FAILURE'];
    // const Pending = ['P', 'PENDING'];
    // const Unknown = ['U', 'UNKNOWN'];
    // const Refund = ['R','REFUND'];
    // const Chargeback = ['K', 'CHARGEBACK'];
    // const Void = ['V', 'VOID'];
    // const Authorized = ['A', 'AUTHRORIZED'];
}
