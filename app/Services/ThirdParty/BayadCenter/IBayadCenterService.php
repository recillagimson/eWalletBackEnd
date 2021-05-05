<?php

namespace App\Services\ThirdParty\BayadCenter;

use Illuminate\Http\Client\Response;

interface IBayadCenterService
{
    public function getToken();
}
