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
        $current_balance = 0;
        $available_balance = 0;
        $current_id = "";
        foreach($this->data as $entry) {

            // Check if need to add balance base on user_account_id
            if($current_id != $entry->account_number) {
                $current_balance = 0;
                $available_balance = 0;
                $current_id = $entry->account_number;
            } else {
                // CHECK Transaction type for Current Balance
                if($entry->Type == 'CR') {
                    $current_balance = $current_balance + (Float) $entry->total_amount;
                } else {
                    $current_balance = $current_balance - (Float) $entry->total_amount;
                }

                // Check for available balance
                if($entry->Status == 'SUCCESS') {
                    if($entry->Type == 'CR') {
                        $available_balance = $available_balance + (Float) $entry->total_amount;
                    } else {
                        $available_balance = $available_balance - (Float) $entry->total_amount;
                    }
                }
            }

            $proc = [
                $entry->transaction_date,
                $entry->first_name . " " . $entry->last_name,
                $entry->account_number,
                strval($current_balance),
                $entry->Type == 'CR' ? 'Credit' : 'Debit',
                $entry->reference_number,
                strval($entry->total_amount),
                "N/A",
                strval($available_balance),
                $entry->Status
            ];

            array_push($processed_data, $proc);
        }

        return $processed_data;
    }
}
