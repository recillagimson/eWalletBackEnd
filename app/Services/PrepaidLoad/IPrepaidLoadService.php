<?php

namespace App\Services\PrepaidLoad;

interface IPrepaidLoadService
{
    public function loadGlobe(array $items): string;
}

