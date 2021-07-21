<?php

namespace App\Repositories\InAddMoney;

use App\Repositories\IRepository;
use Illuminate\Database\Eloquent\Collection;

interface IInAddMoneyRepository extends IRepository
{
    public function getLastByReferenceNumber();

    public function getByReferenceNumber(string $referenceNumber);

    public function getLatestPendingByUserAccountID(string $userAccountID);

    public function getUserPending(string $userId): Collection;

    public function getByMultipleReferenceNumber(array $referenceNumbers);

    public function getByUserAccountID(string $userAccountID);

    public function getSumOfTransactions($from, $to, $userAccountID);

    public function getTotalAddMoney();
}
