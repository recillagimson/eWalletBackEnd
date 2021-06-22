<?php

namespace App\Services\Utilities\CSV;

interface ICSVService
{
    public function generateCSV(array $datas, string $filename, array $columns);
}
