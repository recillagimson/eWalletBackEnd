<?php

namespace App\Services\ThirdParty\GH;

use Illuminate\Http\Client\Response;

interface IGHService
{
    public function print(array $data): Response;
}
