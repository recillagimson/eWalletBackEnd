<?php

namespace App\Services\Auth\Registration;

use Illuminate\Validation\ValidationException;

interface IRegistrationService
{
    /**
     * Validation policies for account registration
     *
     * @param string $usernameField
     * @param string $username
     * @throws ValidationException
     */
    public function validateAccount(string $username, string $usernameField);

    /**
     * Creates a new UserAccount record
     *
     * @param array $newUser
     * @param string $usernameField
     * @return mixed
     * @throws ValidationException
     */
    public function register(array $newUser, string $usernameField);

    /**
     * Activates the user account by validating the otp
     *
     * @param string $usernameField
     * @param string $username
     * @param string $otp
     * @return mixed
     */
    public function verifyAccount(string $usernameField, string $username, string $otp);
}
