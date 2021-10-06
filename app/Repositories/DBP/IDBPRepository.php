<?php

namespace App\Repositories\DBP;

use App\Repositories\IRepository;

interface IDBPRepository extends IRepository
{
    public function customerList($from, $to, $filterBy, $filterValue, $isPaginated = false);
    public function disbursement($from, $to, $filterBy, $filterValue, $isPaginated = false);
    public function memo($from, $to, $filterBy, $filterValue, $isPaginated = false);
    public function onBoardingList($from, $to, $filterBy, $filterValue, $isPaginated = false);
    public function transactionHistories($from, $to, $filterBy, $filterValue, $isPaginated = false);
    public function claims($from, $to, $filterBy, $filterValue, $isPaginated = false);
}
