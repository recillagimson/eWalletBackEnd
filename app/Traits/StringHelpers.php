<?php

namespace App\Traits;

use Carbon\Carbon;

trait StringHelpers
{
    public function formatDate(Carbon $date): string
    {
        return $date->timezone('Asia/Manila')->toDateTimeString();
    }

    public function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', ',');
    }
}
