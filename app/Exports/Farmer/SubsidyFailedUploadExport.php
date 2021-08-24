<?php

namespace App\Exports\Farmer;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubsidyFailedUploadExport implements FromCollection, WithHeadings
{
    private $rows;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }
    /** 
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'remarks',
            'account_number',
            'vw_farmerprofile_full_wm.rsbsa_no',
            'amount',
            'batch_code',
        ];
    }
}
