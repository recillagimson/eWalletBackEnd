<?php

namespace App\Exports\DRCR;

use App\Traits\LogHistory\LogHistory;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DRCRReport implements FromArray, WithHeadings, ShouldAutoSize
{

    use Exportable, LogHistory;

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
        return $this->processData($this->data);
    }
}
