<?php

namespace App\Services\Auth\UserKey;

use App\Models\UserAccount;

interface IUserKeyService
{
    /**
     * Validates a user inputs and generates otp
     *
     * @param string $userId
     * @param string $currentKey
     * @param string $newKey
     * @param string $keyType
     * @param bool $requireOtp
     * @return mixed
     */
    public function validateKey(string $userId, string $currentKey, string $newKey, string $keyType,
                                bool   $requireOtp = true);

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

    /**
     * Updates the authenticated user pin/password
     *
     * @param string $userId
     * @param string $currentKey
     * @param string $newKey
     * @param string $keyType
     * @param string $otpType
     * @param bool $requireOtp
     * @return mixed
     */
    public function changeKey(string $userId, string $currentKey, string $newKey, string $keyType, string $otpType,
                              bool   $requireOtp = true);

    /**
     * Validate if current pin / password matches the current user
     *
     * @param UserAccount|null $user
     * @param string|null $keyType
     * @param string|null $userKey
     * @return mixed
     */
    public function validateUser(?UserAccount $user, string $keyType = null, string $userKey = null);
}
