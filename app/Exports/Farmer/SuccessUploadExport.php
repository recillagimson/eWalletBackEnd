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
            'USER ACCOUNT NUMBER',
            "RSBSA REFERENCE NUMBER",
            "FIRSTNAME",
            "MIDDLENAME",
            "LASTNAME",
            "EXTENSIONNAME",
            "IDNUMBER",
            "GOVTIDTYPE",
            "STREETNO_PUROKNO",
            "BARANGAY",
            "CITYMUNICIPALITY",
            "DISTRICT",
            "PROVINCE",
            "REGION",
            "BIRTHDATE(YYYY-MM-DD)",
            "PLACEOFBIRTH",
            "MOBILENO",
            "SEX",
            "NATIONALITY",
            "PROFESSION",
            "SOURCEOFFUNDS",
            "MOTHERMAIDENNAME",
            "# OF FARM PARCEL",
            "TOTAL FARM AREA (Ha)",
        ];
    }
}
