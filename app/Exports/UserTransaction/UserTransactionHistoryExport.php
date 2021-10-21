<?php

namespace App\Exports\UserTransaction;

use Carbon\Carbon;
use Illuminate\View\View;
use App\Enums\DBPUploadKeys;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\FromCollection;

class UserTransactionHistoryExport implements WithHeadings, WithEvents, FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    
    private $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $records = [];
        foreach($this->data as $entry) {
            $record = [
                $entry['manila_time_transaction_date'],
                $entry['name'],
                $entry['reference_number'],
                $entry['transaction_type'] == 'DR' ? $entry['total_amount'] : '',
                $entry['transaction_type'] == 'CR' ? $entry['total_amount'] : '',
                $entry['available_balance']
            ];
            array_push($records, $record);
        }

        return view('reports.transaction_history.transaction_history_v2', [
            'records' => $records,
        ]);
    }

    public function collection()
    {
        $records = [];
        foreach($this->data as $entry) {
            $record = [
                $entry['manila_time_transaction_date'],
                $entry['name'],
                $entry['reference_number'],
                $entry['transaction_type'] == 'DR' ? $entry['total_amount'] : '',
                $entry['transaction_type'] == 'CR' ? $entry['total_amount'] : '',
                $entry['available_balance']
            ];
            array_push($records, $record);
        }
        return new collection($records);
    }

    public function headings(): array
    {
        return [
            "Date and Time", "Description", "Reference No.", "Debit", "Credit", "Balance"
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class  => function(BeforeExport $event) {
                $event->writer->getDelegate()->getSecurity()->setLockWindows(true);
                $event->writer->getDelegate()->getSecurity()->setLockStructure(true);
                $event->writer->getDelegate()->getSecurity()->setWorkbookPassword("password");
            }
        ];
    }
}
