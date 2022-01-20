<?php

namespace App\Services\BPIService\Models;

class BPIPromoNotifcation
{
    public string $firstName;
    public float $amount;
    public string $refNo;

    public function __construct(string $firstName, float $amount, string $refNo)
    {
        $this->firstName = $firstName;
        $this->amount = $amount;
        $this->refNo = $refNo;
    }
}
