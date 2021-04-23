<?php


namespace App\Enums;


class OtpTypes
{
    public const login = 'login';
    public const registration = 'registration';
    public const passwordRecovery = 'password_recovery';
    public const pinRecovery = 'pin_recovery';

    public const values = [
        self::login,
        self::registration,
        self::passwordRecovery,
        self::pinRecovery,
    ];
}
