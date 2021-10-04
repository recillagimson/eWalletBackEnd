<?php

namespace App\Services\DrcrMemo;

use App\Models\UserAccount;

interface IDrcrMemoService
{
    public function getAllList(UserAccount $user, $data, $from = '', $to = '');
    public function getList(UserAccount $user, $data, $per_page = 15, $from = '', $to = '');
    public function show(string $referenceNumber);
    public function getUser(string $accountNumber);
    public function store(UserAccount $user, $data);
    public function updateMemo(UserAccount $user, $data);
    public function approval(UserAccount $user, $data);
    public function report(array $params, string $currentUser = '');
    public function reportFiltered(array $attr);

    public function reportFilteredPerUser(array $attr, $isPerUser);
}
