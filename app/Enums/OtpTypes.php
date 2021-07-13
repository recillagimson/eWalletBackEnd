<?php


namespace App\Enums;


class OtpTypes
{
    public const login = 'login';
    public const registration = 'registration';
    public const passwordRecovery = 'password_recovery';
    public const pinRecovery = 'pin_recovery';
    public const updateProfile = 'update_profile';
    public const updateEmail = 'update_email';
    public const updateMobile = 'update_mobile';


    public const sendMoney = 'send_money';
    public const send2Bank = 'send2bank';

    public const buyLoad = 'buy_load';

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
        self::updateEmail,
        self::updateMobile,
    ];

    /**
     * INCLUDE HERE ALL THE TRANSACTION OTP TYPE VALUES ONLY
     */
    public const transactionOtps = [
        self::sendMoney,
        self::send2Bank,
        self::updateProfile,
        self::updateEmail,
        self::updateMobile,
    ];
}
