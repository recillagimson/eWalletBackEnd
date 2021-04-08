<?php


namespace App\Repositories\PasswordHistory;


use App\Repositories\IRepository;

interface IPasswordHistoryRepository extends IRepository
{
    public function getLatest(string $userId);
    public function log(string $userId, string $password);
}
