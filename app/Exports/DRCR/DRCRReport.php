<?php

namespace App\Exports\DRCR;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DRCRReport implements FromArray, WithHeadings, ShouldAutoSize
{

    use Exportable;

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */

    // $file_name = $params['from'] . "-" . $params['to'] . "." . $params['type'];

    
    /**
     * Optional Writer Type
     */
    // private $writerType = Excel::XLSX;
    
    /**
     * Optional headers
     */
    // private $headers = [
    //     'Content-Type' => 'text/xlsx',
    // ];
    
    // private $fileName = $this->from . "-" . $this->to . "." . ;

    private $fileName;
    private $from;
    private $to;
    private $type;
    private $data;
    
    public function __construct(Collection $data, string $from, $to, $type)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
        $this->type = $type;

        // $this->fileName = $from . "-" . $to . "." . $type;
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
