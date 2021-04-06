<?php

namespace App\Services\AddMoney\DragonPay\PostBack;

interface IHandlePostBackService
{
    public function insertPostBackData(array $postBackData);
}