<?php

namespace App\Exports\Farmer\Subsidy;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;


class SubsidyFailedExport implements FromCollection, WithHeadings
{
    private $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            "REMARKS",
            "USER ACCOUNT NUMBER",
            "RSBSA NUMBER",
            "AMOUNT",
            "BATCH CODE",
            "PROGRAM CODE"
        ];
    }
}
