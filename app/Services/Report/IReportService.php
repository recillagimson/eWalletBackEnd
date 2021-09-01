<?php

namespace App\Services\Report;

/**
 * @property 
 * @property 
 *
 */
interface IReportService
{
    public function billersReport(array $params, string $currentUser);
    public function drcrmemofarmers(array $attr);
    public function transactionReportFarmers(array $attr);
    public function farmersList(array $attr);
    public function transactionReportAdmin(array $attr);
    public function customerList(array $attr);
}
