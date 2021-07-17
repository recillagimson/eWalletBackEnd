<?php

namespace App\Services\Report;

use Carbon\Carbon;
use App\Enums\SuccessMessages;
use App\Exports\Biller\BillerReport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Services\Utilities\PDF\IPDFService;
use App\Services\Utilities\Responses\IResponseService;

class ReportService implements IReportService
{

    private IOutPayBillsRepository $payBills;
    private IPDFService $pdfService;
    private IResponseService $responseService;
    public function __construct(IOutPayBillsRepository $payBills, IPDFService $pdfService, IResponseService $responseService)
    {
        $this->payBills = $payBills;
        $this->pdfService = $pdfService;
        $this->responseService = $responseService;
    }

    public function billersReport(array $params, string $currentUser) {

        $from = Carbon::now()->subDays(30)->format('Y-m-d H:i:s');
        $to = Carbon::now()->format('Y-m-d H:i:s');
        $filterBy = '';
        $filterValue = '';
        $type = 'XLSX';

        if($params && isset($params['type'])) {
            $type = $params['type'];
        }

        if($params && isset($params['from']) && isset($params['to'])) {
            $from = $params['from'];
            $to = $params['to'];
        }

        if($params && isset($params['filterBy']) && isset($params['filterValue'])) {
            $filterBy = $params['filterBy'];
            $filterValue = $params['filterValue'];
        }

        $records = $this->payBills->reportData($from, $to, $filterBy, $filterValue);
        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        if($params['type'] == 'PDF') {

            Excel::store(new BillerReport($records, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::MPDF);
            $temp_url = $this->s3TempUrl($fileName);
            // $data = $this->processData($data);
            // $records = [
            //     'records' => $records
            // ];
            // ini_set("pcre.backtrack_limit", "5000000");
            // $file = $this->pdfService->generatePDFNoUserPassword($records, 'reports.out_pay_bills_history.out_pay_bills_history_report', true);
            // $url = $this->storeToS3($currentUser, $file['file_name'], $fileName);
            // unlink($file['file_name']);
            // $temp_url = $this->s3TempUrl($url);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } 
        else if($params['type'] == 'CSV') {
            Excel::store(new BillerReport($records, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } 
        else if($params['type'] == 'API')  {
            return $this->responseService->successResponse($records, SuccessMessages::success);
        }
        else {
            Excel::store(new BillerReport($records, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        }
    }

    public function storeToS3(string $currentUser, $file, string $fileName) {
        $folderName = 'reports/' . $currentUser;
        $generated_link = Storage::disk('s3')->putFileAs($folderName, $file, $fileName);
        return $generated_link;
    }

    public function s3TempUrl(string $generated_link) {
        $temp_url = Storage::disk('s3')->temporaryUrl($generated_link, Carbon::now()->addMinutes(30));
        return $temp_url;
    }
}
