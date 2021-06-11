<?php

namespace App\Services\DrcrMemo;

use App\Models\UserAccount;

interface IDrcrMemoService
{
    public function getList();
    public function store($data, UserAccount $user);
}
