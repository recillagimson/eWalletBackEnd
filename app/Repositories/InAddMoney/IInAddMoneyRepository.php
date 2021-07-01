<?php

namespace App\Repositories\InAddMoney;

use App\Repositories\IRepository;

interface IInAddMoneyRepository extends IRepository
{
    public function getLastByReferenceNumber();
    public function getByReferenceNumber(string $referenceNumber);
    public function getLatestPendingByUserAccountID(string $userAccountID);
    public function getByMultipleReferenceNumber(array $referenceNumbers);
    public function getByUserAccountID(string $userAccountID);
    public function getSumOfTransactions($from, $to, $userAccountID);
    public function getTotalAddMoney();
}