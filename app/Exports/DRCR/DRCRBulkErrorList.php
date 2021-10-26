<?php

namespace App\Exports\DRCR;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class DRCRBulkErrorList implements FromCollection, WithHeadings
{
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
            "ROW NUMBER",
            "ACCOUNT NUMBER",
            "TYPE OF MEMO",
            "CATEGORY",
            "AMOUNT",
            "CURRENCY",
            "TRANSACTION DESCRIPTION",
            "REMARKS",
        ];
    }
}
