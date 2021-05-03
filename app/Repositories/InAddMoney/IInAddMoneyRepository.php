<?php

namespace App\Repositories\InAddMoney;

use App\Repositories\IRepository;

interface IInAddMoneyRepository extends IRepository
{
    public function getLastByReferenceNumber();
    public function getByReferenceNumber(string $referenceNumber);
    public function getLatestPendingByUserAccountID(string $userAccountID);
    public function getByMultipleReferenceNumber(array $referenceNumbers);
    public function getBetweenDates(string $startdate, string $enddate);
}