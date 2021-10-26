<?php

namespace App\Exports\Biller;

use Illuminate\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BillerReport implements FromView, WithHeadings, WithEvents
{

    use Exportable;
    private $fileName;
    private $from;
    private $to;
    private $type;
    private $data;
    private $records = [];
    
    public function __construct(Collection $data, string $from, $to, $type)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
        $this->type = $type;

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

        $this->records = $data;

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

    public function view(): View
    {
        return view('reports.out_pay_bills_history.out_pay_bills_history_report', [
            'records' => $this->records
        ]);
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

    public function registerEvents(): array
    {
        $count = count($this->records);
        $type = $this->type;
        return [
            AfterSheet::class => function (AfterSheet $event) use($count, $type) {
                $i = 0;
                while($i < $count) {
                    $cells = 'A' . ($i + 1) . ":G" . ($i + 1);
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
                            'size' => 16,
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
                } else {
                    $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(50);
                }
                
            },
        ];
    }
}
