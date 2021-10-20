<?php

namespace App\Exports\Farmer\Export;

use App\Enums\DBPUploadKeys;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class SuccessExport implements FromCollection, WithHeadings
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
        $successData = [];
        foreach($this->data as $entry) {
            array_push($successData, [
                $entry['rsbsa_number'],
                $entry['firstname'],
                $entry['middlename'],
                $entry['lastname'],
                $entry['extensionname'],
                $entry['idnumber'],
                $entry['govtidtype'],
                $entry['streetno_purokno'],
                $entry['barangay'],
                $entry['citymunicipality'],
                $entry['district'],
                $entry['province'],
                $entry['region'],
                $entry[DBPUploadKeys::birthDate],
                $entry['placeofbirth'],
                $entry['mobileno'],
                $entry['sex'],
                'Filipino',
                'Farmer',
                'Farming',
                $entry[DBPUploadKeys::mothermaidenname],
                $entry[DBPUploadKeys::farmParcel],
                $entry[DBPUploadKeys::totalFarmArea],
                $entry['account_number'],
                'SUCCESS'
            ]);
        }
        return new collection($successData);
    }

    public function headings(): array
    {
        return [
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
