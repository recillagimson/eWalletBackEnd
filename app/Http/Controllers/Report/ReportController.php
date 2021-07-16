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
        $response = $this->reportService->billersReport($request->all(), request()->user()->id);
    }
}
