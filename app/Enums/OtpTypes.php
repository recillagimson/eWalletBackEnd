<?php


namespace App\Enums;


class OtpTypes
{
    public const login = 'login';
    public const registration = 'registration';
    public const passwordRecovery = 'password_recovery';
    public const pinRecovery = 'pin_recovery';

    /**
     * INCLUDE HERE ALL THE OTP TYPE VALUES ABOVE
     */
    public const values = [
        self::login,
        self::registration,
        self::passwordRecovery,
        self::pinRecovery,
    ];

    /**
     * INCLUDE HERE ALL THE TRANSACTION OTP TYPE VALUES ONLY
     */
    public const transactionOtps = [
    ];
}
