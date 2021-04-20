<?php

namespace App\Services\Utilities\ReferenceNumber;

interface IReferenceNumberService
{
    public function generate(string $referenceType): string;
}
