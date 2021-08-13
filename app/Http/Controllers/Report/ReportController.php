<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\Report\IReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    private IReportService $reportService;
    public function __construct(IReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function billerReport(Request $request) {
        return $this->reportService->billersReport($request->all(), request()->user()->id);
    }

    public function DRCRMemoFarmers(Request $request) {
        return $this->reportService->drcrmemofarmers($request->all());
    }

    public function TransactionReportFarmers(Request $request) {
        return $this->reportService->transactionReportFarmers($request->all());
    }

    public function FarmersList(Request $request) {
        return $this->reportService->farmersList($request->all());
    }
  
}
