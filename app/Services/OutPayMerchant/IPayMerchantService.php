<?php

namespace App\Services\OutPayMerchant;


use Illuminate\Database\Eloquent\Collection;

interface IPayMerchantService
{
    public function pay(array $data);
    public function getByReferenceNumber(string $refNo): Collection;
}
