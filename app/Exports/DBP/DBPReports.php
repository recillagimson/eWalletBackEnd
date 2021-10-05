<?php

namespace App\Exports\DBP;

use Illuminate\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class DBPReports implements FromView, WithEvents
{
    private $data;
    private $headers;
    private $viewName;
    public function __construct(Collection $data, array $headers, string $viewName)
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->viewName = $viewName;
    }

    public function view(): View
    {
        return view($this->viewName, [
            'records' => $this->data,
            'headers' => $this->headers
        ]);
    }

    public function registerEvents(): array
    {
        $count = count($this->data);
        return [
            AfterSheet::class => function (AfterSheet $event) use($count) {
                $i = 0;
                while($i <= $count) {
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
                    $event->sheet->getDelegate()->getRowDimension($i+1)->setRowHeight(20);
                    $i++;
                }

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
                
            },
        ];
    }
}
