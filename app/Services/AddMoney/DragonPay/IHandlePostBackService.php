<?php

namespace App\Services\AddMoney\DragonPay;

interface IHandlePostBackService
{
    public function insertPostBackData(array $postBackData);
}