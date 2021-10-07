<?php

namespace App\Services\UBP;

interface IUbpAccountService
{
    public function checkAccountLink(string $userId);
}
