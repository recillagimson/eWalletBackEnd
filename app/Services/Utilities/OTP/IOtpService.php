<?php

namespace App\Services\Utilities\OTP;

interface IOtpService
{
    public function generate(string $identifier): object;
    public function validate(string $identifier, string $token): object;
    public function ensureValidated(string $identifier);
    public function expiredAt(string $identifier): object;
}
