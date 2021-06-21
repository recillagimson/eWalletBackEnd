<?php

namespace App\Services\DrcrMemo;

use App\Models\UserAccount;

interface IDrcrMemoService
{
    public function getList(UserAccount $user);
    public function show(string $referenceNumber);
    public function getUser(string $accountNumber);
    public function showPending(UserAccount $user);
    public function store(UserAccount $user, $data);
    public function approval(UserAccount $user, $data);
}
