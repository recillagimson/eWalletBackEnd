<?php


namespace App\Repositories\UserKeys;


interface IUserKeyLogsRepository
{
    public function getLatest(string $userId);

    public function getPrevious(int $takeCount, string $userId);

    public function log(string $userId, string $key);
}
