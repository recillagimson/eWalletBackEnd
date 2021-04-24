<?php

namespace App\Models\UserKeys;

interface IUserKeyModel
{
    /**
     * Get the key's age in days
     *
     * @return int
     */
    public function getKeyAgeAttribute(): int;

    /**
     * Check if the key is about to expire
     * based on grace period
     *
     * @param int $daysToNotify
     * @param int $maxAge
     * @return bool
     */
    public function isAboutToExpire(int $daysToNotify = 15, int $maxAge = 60): bool;

    /**
     * Check if key has reached its minimum age
     *
     * @param int $minAge
     * @return bool
     */
    public function isAtMinimumAge(int $minAge = 1): bool;
}
