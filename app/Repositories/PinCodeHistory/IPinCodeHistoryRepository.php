<?php


namespace App\Repositories\PinCodeHistory;


use App\Repositories\IRepository;

interface IPinCodeHistoryRepository extends IRepository
{
    public function getLatest(string $userId);
    public function log(string $userId, string $pinCode);
}
