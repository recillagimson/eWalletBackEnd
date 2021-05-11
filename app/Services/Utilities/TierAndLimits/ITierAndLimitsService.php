<?php

namespace App\Services\Utilities\TierAndLimits;

use App\Models\UserAccount;

interface ITierAndLimitsService {
    public function validateTierAndLimits(float $amount, string $squidPayModule);
}
