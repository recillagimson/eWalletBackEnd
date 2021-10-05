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
        $headerCount = count($this->headers);
        return [
            AfterSheet::class => function (AfterSheet $event) use($count, $headerCount) {
                $i = 0;
                $upperArr = range('A', 'Z') ;
                while($i <= $count) {
                    $cells = 'A' . ($i + 1) . ":" . $upperArr[($headerCount - 1)] . ($i + 1);
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

                $i = 0;
                while($i < $headerCount) {
                    $event->sheet->getDelegate()->getColumnDimension($upperArr[$i])->setWidth(50);
                    $i++;
                }
            },
        ];
    }
}
