<?php

namespace App\Exports\DRCR;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\View\View;
use App\Traits\LogHistory\LogHistory;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DRCRReport implements WithHeadings, FromView, WithEvents
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
    private $records;
    
    public function __construct(Collection $data, string $from, $to, $type)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
        $this->type = $type;
        $this->records = $this->processData($this->data);

        // $this->fileName = $from . "-" . $to . "." . $type;
    }

    public function view(): View
    {
        return view('reports.log_histories.log_histories', [
            'records' => $this->processData($this->data)
        ]);
    }

    public function headings(): array
    {
        return [
            'Transaction Date', 'Customer ID', 'Customer Name', 'Current Balance', 'Type (DR/CR)', 'Category', 'Amount',
            'Transaction Description', 'Available Balance', 'Status'
        ];
    }

    public function registerEvents(): array
    {
        $count = count($this->records);
        return [
            AfterSheet::class => function (AfterSheet $event) use($count) {
                $i = 0;
                while($i < $count) {
                    $cells = 'A' . ($i + 1) . ":J" . ($i + 1);
                    // dd($cells);
                    $style = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'font' => [
                            'size' => 15,
                            'color' => ['argb' => '000'],
                        ]
                    ];

                    if($i == 0) {

                        // $style['font'] =;
                        $style['fill'] = [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'e9cc8a'],
                        ];
                    }

                    $event->sheet->getStyle($cells)->applyFromArray($style);
                    $event->sheet->getDelegate()->getStyle($cells)->getFont()->setSize(60);
                    // $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(100);
                    $event->sheet->getDelegate()->getRowDimension($i+1)->setRowHeight(20);
                    $i++;
                }
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(100);
            },
        ];
    }


}
