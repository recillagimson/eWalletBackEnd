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
}
