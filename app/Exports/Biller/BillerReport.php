<?php

namespace App\Exports\Biller;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BillerReport implements FromArray, WithHeadings, ShouldAutoSize
{

    use Exportable;
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
            'Account Number',
            'Name',
            'Reference Number',
            'Transactionm Date',
            'Biller',
            'Total Amount',
            'Status',
        ];
    }

    public function array(): array
    {
        // dd($this->data);
        $data = [];
        foreach($this->data as $record) {
            array_push($data, [
                $record['account_number'],
                $record['first_name'] . " " . $record['middle_name'] . " " . $record['last_name'],
                $record['reference_number'],
                $record['transaction_date'],
                $record['billers_name'],
                $record['total_amount'],
                $record['status'],
            ]);
        }
        return $data;
    }
}
