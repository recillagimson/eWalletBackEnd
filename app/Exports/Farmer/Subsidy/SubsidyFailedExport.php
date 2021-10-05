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
            "FUNDING CURRENCY",
            "REMITTANCE DATE",
            "SERVICE CODE",
            "APPLICATION NUMBER",
            "REMITTANCE NUMBER",
            "REMARKS (RSBSA NUMBER)",
            "OUTLET NAME",
            "Beneficiary Name 1",
            "Beneficiary Name2",
            "Beneficiary Name3",
            "Beneficiary Address1",
            "Beneficiary Address2",
            "Beneficiary Address3",
            "Beneficiary Mobile Number",
            "Message",
            "Remitter Name1",
            "Remitter Name_2",
            "Remitter Address 1",
            "Remitter Address 2",
            "Transaction Category Id",
        ];
    }
}
