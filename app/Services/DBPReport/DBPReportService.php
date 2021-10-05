<?php

namespace App\Services\DBPReport;

use Carbon\Carbon;
use App\Enums\SuccessMessages;
use App\Exports\DBP\DBPReports;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Repositories\DBP\IDBPRepository;
use App\Services\Utilities\Responses\IResponseService;

class DBPReportService implements IDBPReportService
{

    private IDBPRepository $dbpRepository;
    private IResponseService $responseService;
    public function __construct(IDBPRepository $dbpRepository, IResponseService $responseService)
    {
        $this->dbpRepository = $dbpRepository;
        $this->responseService = $responseService;
    }

    private function s3TempUrl(string $generated_link) {
        $temp_url = Storage::disk('s3')->temporaryUrl($generated_link, Carbon::now()->addMinutes(30));
        return $temp_url;
    }

    public function customerList(array $attr) {
        
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';
        $filterBy = '';
        $filterValue = '';
        $headers = [
            'RSBSA Number',
            'Account Number',
            'First Name',
            'Middle Name',
            'Last Name',
            'Account Status',
            'Profile Status',
            'Registration Date',
        ];

        if($attr && isset($attr['filterBy']) && isset($attr['filterValue'])) {
            $filterBy = $attr['filterBy'];
            $filterValue = $attr['filterValue'];
        }

        if($attr && isset($attr['from'])) {
            $from = $attr['from'];
        }
        if($attr && isset($attr['to'])) {
            $to = $attr['to'];
        }

        if($attr && isset($attr['type'])) {
            $type = $attr['type'];
        }

        $records = [];
        if($attr && isset($attr['type']) && $attr['type'] == 'API') {
            $records = $this->dbpRepository->customerList($from, $to, $filterBy, $filterValue, true);
        } else {
            $records = $this->dbpRepository->customerList($from, $to, $filterBy, $filterValue, false);
        }

        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        if($type === 'CSV') {
            Excel::store(new DBPReports($records, $headers, 'reports.dbp.customer_list'), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);

            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } else if($type === 'XLSX') {
            Excel::store(new DBPReports($records, $headers, 'reports.dbp.customer_list'), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);

        } else {
            // return $records->toArray();
            return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
        }
    }
}
