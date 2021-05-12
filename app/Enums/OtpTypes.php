<?php


namespace App\Enums;


class OtpTypes
{
    public const login = 'login';
    public const registration = 'registration';
    public const passwordRecovery = 'password_recovery';
    public const pinRecovery = 'pin_recovery';

    public const sendMoney = 'send_money';
    public const send2Bank = 'send2bank';
    public const updateProfile = 'update_profile';

    /**
     * INCLUDE HERE ALL THE OTP TYPE VALUES ABOVE
     */
    public const values = [
        self::login,
        self::registration,
        self::passwordRecovery,
        self::pinRecovery,
        self::updateProfile,
        self::sendMoney,
        self::send2Bank,

    ];

    /**
     * INCLUDE HERE ALL THE TRANSACTION OTP TYPE VALUES ONLY
     */
    public const transactionOtps = [
        self::sendMoney,
        self::send2Bank,
        self::updateProfile,
    ];
}
