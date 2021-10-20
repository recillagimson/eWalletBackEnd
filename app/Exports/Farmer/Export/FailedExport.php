<?php

namespace App\Exports\Farmer\Export;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class FailedExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    private $headers;
    private $data;
    public function __construct(Collection $data, array $headers)
    {
        $this->headers = $headers;
        $this->data = $data;
    }

    public function collection()
    {
        // return $this->data;
        $records = [];
        foreach($this->data as $item) {
            $data = $item;
            $data['account_number'] = 'N/A';
            $data['remarks'] = 'FAILED';
        }
        return new Collection($records);
    }

    public function headings(): array
    {
        return [
            "REMARKS",
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
            "ACCOUNT NUMBER",
            "REMARKS"
        ];
    }
}
