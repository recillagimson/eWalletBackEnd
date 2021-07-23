<?php

namespace App\Enums;

class UsernameTypes
{
    const Email = 'email';
    const MobileNumber = 'mobile_number';

    public const usernameTypes = [
        self::Email,
        self::MobileNumber,
    ];
}
