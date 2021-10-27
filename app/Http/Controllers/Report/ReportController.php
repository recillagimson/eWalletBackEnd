<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Report\IReportService;
use Illuminate\Support\Facades\Storage;
use App\Services\Printing\IPrintService;
use App\Http\Requests\Report\PrintRequest;
use App\Http\Requests\S3\S3GenerateLinkRequest;
use App\Services\Utilities\Responses\IResponseService;

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
        return $this->responseService->successResponse([]);
    }

    public function generateS3Link(S3GenerateLinkRequest $request) {
        $record = Storage::disk('s3')->temporaryUrl($request->file_path, Carbon::now()->addMinutes(30));
        return $this->responseService->successResponse([
            'link' => $record
        ]);
    }
}
