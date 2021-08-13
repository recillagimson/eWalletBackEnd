<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\PrintRequest;
use App\Services\Printing\IPrintService;
use App\Services\Report\IReportService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    private IReportService $reportService;
    private IPrintService $printService;
    private IResponseService $responseService;

    public function __construct(IReportService   $reportService,
                                IPrintService    $printService,
                                IResponseService $responseService)
    {
        $this->reportService = $reportService;
        $this->printService = $printService;
        $this->responseService = $responseService;
    }

    public function billerReport(Request $request)
    {
        return $this->reportService->billersReport($request->all(), request()->user()->id);
    }

    public function DRCRMemoFarmers(Request $request)
    {
        return $this->reportService->drcrmemofarmers($request->all());
    }

    public function TransactionReportFarmers(Request $request)
    {
        return $this->reportService->transactionReportFarmers($request->all());
    }

    public function FarmersList(Request $request) {
        return $this->reportService->farmersList($request->all());
    }
  
    public function print(PrintRequest $request): JsonResponse
    {
        $this->printService->print($request->validated());
        return $this->responseService->successResponse();
    }
}
