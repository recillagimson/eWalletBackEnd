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
    public function getByUserAccountIDAndStatus(string $userAccountID, string $status);
    public function getByUserAccountIDBetweenDates(string $userAccountID, string $startDate, string $endDate);
}