<?php
namespace App\Repositories\UserBalanceInfo;

use App\Repositories\IRepository;

interface IUserBalanceInfoRepository extends IRepository
{
    public function getUserBalance(string $userID);
    public function updateUserBalance(string $userID, float $newBalance);
}
