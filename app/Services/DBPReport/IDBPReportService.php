<?php

namespace App\Services\DBPReport;

/**
 * @property 
 * @property 
 *
 */
interface IDBPReportService
{
    public function customerList(array $attr);
    public function disbursement(array $attr);
    public function memo(array $attr);
    public function onBoardingList(array $attr);
    public function transactionHistories(array $attr);
    public function claims(array $attr);
}
