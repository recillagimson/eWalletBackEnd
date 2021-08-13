<?php

namespace App\Exports\Farmer;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuccessUploadExport implements FromCollection, WithHeadings
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
            'user_account_number',
            'vw_farmerprofile_full_wm.rsbsa_no',
            'vw_farmerprofile_full_wm.fname',
            'vw_farmerprofile_full_wm.mname',
            'vw_farmerprofile_full_wm.lname',
            'vw_farmerprofile_full_wm.ext_name',
            'vw_farmerprofile_full_wm.mother_maiden_name',
            'vw_farmerprofile_full_wm.sex',
            'vw_farmerprofile_full_wm.house_no',
            'vw_farmerprofile_full_wm.street',
            'vw_farmerprofile_full_wm.bgyname',
            'vw_farmerprofile_full_wm.munname',
            'vw_farmerprofile_full_wm.provname',
            'vw_farmerprofile_full_wm.regshortname',
            'vw_farmerprofile_full_wm.contact_num',
            'vw_farmerprofile_full_wm.education',
            'vw_farmerprofile_full_wm.birthdate',
            'birthplace',
            'vw_farmerprofile_full_wm.civil_status',
            'vw_farmerprofile_full_wm.religion',
            'vw_farmerprofile_full_wm.gross_income_farming',
            'vw_farmerprofile_full_wm.gross_income_nonfarming',
            'govid.id_type',
            'vw_farmerprofile_full_wm.gov_id_num',
        ];
    }
}
