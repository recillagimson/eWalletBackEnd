<?php
namespace App\Repositories\UserBalanceInfo;

use App\Repositories\IRepository;
use App\Models\UserBalanceInfo;

interface IUserBalanceInfoRepository extends IRepository
{
    public function getUserBalance(string $userID);
    public function updateUserBalance(string $userID, float $newBalance);
}
