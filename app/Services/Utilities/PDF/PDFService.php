<?php


namespace App\Services\Utilities\PDF;

use App\Repositories\OutPayBills\IOutPayBillsRepository;
use PDF;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PDFService implements IPDFService
{
    
    public IOutPayBillsRepository $outPayBillsRepository;

    public function __construct(IOutPayBillsRepository $outPayBillsRepository)
    {
        $this->outPayBillsRepository = $outPayBillsRepository;
    }

    public function generatePDFNoUserPassword(array $data, string $loadView, $save = false) {
        $datetimeNow = Carbon::now()->timestamp;
        $file_name = "admin_" . $datetimeNow . '.pdf';

        $pdf = PDF::loadView($loadView, $data);
        $pdf->SetProtection(['copy', 'print'], '', 'squidP@y');

        if($save) {
            return [
                'file' => $pdf->save($file_name),
                'file_name' => $file_name
            ];
        }
        return $pdf->stream($file_name);

    }
}
