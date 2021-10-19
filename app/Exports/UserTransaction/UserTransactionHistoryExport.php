<?php

namespace App\Exports\UserTransaction;

use App\Enums\DBPUploadKeys;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UserTransactionHistoryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $records = [];
        foreach($this->data as $entry) {

        }
        return new collection($records);
    }

    public function headings(): array
    {
        return [
            "Date and Time", "Description", "Reference No.", "Debit", "Credit", "Balance"
        ];
    }
}
