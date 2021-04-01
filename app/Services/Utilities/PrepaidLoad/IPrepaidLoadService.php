<?php

namespace App\Services\Utilities\PrepaidLoad;

interface IPrepaidLoadService
{
    public function load(array $items): array;
    public function showNetworkPromos(): array;
}

