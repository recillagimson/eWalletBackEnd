<?php


namespace App\Enums;


class UserKeyTypes
{
    const password = 'password';
    const pin = 'pin';

    const values = [
        self::password,
        self::pin,
    ];
}
