<?php

namespace App\Services\Auth\UserKey;

interface IUserKeyService
{
    /**
     * Generates OTP for password / pin recovery
     *
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otpType
     */
    public function forgotKey(string $usernameField, string $username, string $otpType);

    /**
     * Verifies and validates otp for password recovery
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otp
     * @param string $otpType
     */
    public function verifyKey(string $usernameField, string $username, string $otp, string $otpType);

    /**
     * Change password / pin
     *
     * @param string $usernameField
     * @param string $username
     * @param string $key
     * @param string $keyType
     * @param string $otpType
     * @param bool $requireOtp
     */
    public function resetKey(string $usernameField, string $username, string $key, string $keyType,
                             string $otpType, bool $requireOtp = true);
}
