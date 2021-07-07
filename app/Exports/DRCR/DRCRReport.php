<?php

namespace App\Exports\DRCR;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DRCRReport implements FromArray, WithHeadings, ShouldAutoSize
{

    private $data;
    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Transaction Date', 'Customer ID', 'Customer Name', 'Current Balance', 'Type (DR/CR)', 'Category', 'Amount',
            'Transaction Description', 'Available Balance'
        ];
    }

    public function array(): array
    {
        $processed_data = [];
        foreach($this->data as $entry) {
            dd($entry);
            $proc = [
                $entry->transaction_date,
                $entry->first_name . " " . $entry->last_name,
                $entry->account_number,
                $entry->total_amount,
                $entry->Type,
                "",

            ];
        }
        return [];
    }
}
