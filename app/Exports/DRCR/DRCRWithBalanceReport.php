<?php

namespace App\Exports\DRCR;

use Illuminate\View\View;
use Illuminate\Support\Collection;
use App\Traits\LogHistory\LogHistory;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DRCRWithBalanceReport implements WithHeadings, FromView, WithEvents
{
    private $fileName;
    private $from;
    private $to;
    private $type;
    private $data;
    private $records;

    use Exportable, LogHistory;
    
    public function __construct(Collection $data, string $from, $to, $type)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
        $this->type = $type;
        $this->records = $this->processDataWithRunningBalance($this->data);

        // $this->fileName = $from . "-" . $to . "." . $type;
    }

    public function view(): View
    {
        return view('reports.log_histories.drcr_transaction_running_balance', [
            'records' => $this->records
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
        $type = $this->type;
        return [
            AfterSheet::class => function (AfterSheet $event) use($count, $type) {
                $i = 0;
                while($i <= $count) {
                    $cells = 'A' . ($i + 1) . ":S" . ($i + 1);
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
                            'size' => 12,
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
                    if($type == 'PDF') {
                        $event->sheet->getDelegate()->getStyle($cells)->getFont()->setSize(60);
                    }
                    // $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(100);
                    $event->sheet->getDelegate()->getRowDimension($i+1)->setRowHeight(20);
                    $i++;
                }
                if($type == 'PDF') {
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
                    $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(100);
                    $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(100);
                    $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(100);
                    $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(100);
                    $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(100);
                    $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(100);
                    $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(100);
                    $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(100);
                    $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(100);
                } else {
                    $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(50);
                }
                
            },
        ];
    }
}
