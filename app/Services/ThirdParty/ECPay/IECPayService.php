<?php

namespace App\Services\ThirdParty\ECPay;

use Illuminate\Http\Client\Response;

interface IECPayService
{
    public function commitPayment(array $data, object $user): object;
    public function batchConfirmPayment(string $userId): array;

}
