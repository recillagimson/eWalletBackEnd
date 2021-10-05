<?php

namespace App\Services\DBPReport;

use Carbon\Carbon;
use App\Enums\SuccessMessages;
use App\Exports\DBP\DBPReports;
use Illuminate\Support\Collection;
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

    private function paramsGeneration(array $attr) {
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';
        $filterBy = '';
        $filterValue = '';

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

        return [
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'filterBy' => $filterBy,
            'filterValue' => $filterValue,
        ];
    }

    private function reportGeneration($records, $headers, $reportView, $fileName, $type) {
        if($type === 'CSV') {
            Excel::store(new DBPReports($records, $headers, $reportView), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);

            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } else if($type === 'XLSX') {
            Excel::store(new DBPReports($records, $headers, $reportView), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);

        } else {
            return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
        }
    }

    public function customerList(array $attr) {
        
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
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';
        $filterBy = '';
        $filterValue = '';

        $payload = $this->paramsGeneration($attr);
        $from = $payload['from'];
        $to = $payload['to'];
        $type = $payload['type'];
        $filterBy = $payload['filterBy'];
        $filterValue = $payload['filterValue'];

        $records = [];
        if($attr && isset($attr['type']) && $attr['type'] == 'API') {
            $records = $this->dbpRepository->customerList($from, $to, $filterBy, $filterValue, true);
        } else {
            $records = $this->dbpRepository->customerList($from, $to, $filterBy, $filterValue, false);
        }

        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        return $this->reportGeneration($records, $headers, 'reports.dbp.customer_list', $fileName, $type);
    }

    // DISBURSEMENT
    public function disbursement(array $attr) {
        
        $headers = [
            'Reference Number',
            'Transaction Date',
            'Account Number',
            'RSBSA Number',
            'First Name',
            'Middle Name',
            'Last Name',
            'Address',
            'City/Municipality',
            'Province/State',
            'Mobile Number',
            'Currency',
            'Remarks',
            'Status',
        ];
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';
        $filterBy = '';
        $filterValue = '';

        $payload = $this->paramsGeneration($attr);
        $from = $payload['from'];
        $to = $payload['to'];
        $type = $payload['type'];
        $filterBy = $payload['filterBy'];
        $filterValue = $payload['filterValue'];

        $records = [];

        $records = [];
        if($attr && isset($attr['type']) && $attr['type'] == 'API') {
            $records = $this->dbpRepository->disbursement($from, $to, $filterBy, $filterValue, true);
        } else {
            $records = $this->dbpRepository->disbursement($from, $to, $filterBy, $filterValue, false);
        }

        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        return $this->reportGeneration($records, $headers, 'reports.dbp.disbursement', $fileName, $type);
    }

    // MEMO
    public function memo(array $attr) {
        
        $headers = [
            'RSBSA Number',
            'Account Number',
            'First Name',
            'Middle Name',
            'Last Name',
            'Type',
            'Transaction Date',
            'Reference Number',
            'Category',
            'Total Amount',
            'Description',
            'Status',
        ];
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';
        $filterBy = '';
        $filterValue = '';

        $payload = $this->paramsGeneration($attr);
        $from = $payload['from'];
        $to = $payload['to'];
        $type = $payload['type'];
        $filterBy = $payload['filterBy'];
        $filterValue = $payload['filterValue'];

        $records = [];

        $records = [];
        if($attr && isset($attr['type']) && $attr['type'] == 'API') {
            $records = $this->dbpRepository->memo($from, $to, $filterBy, $filterValue, true);
        } else {
            $records = $this->dbpRepository->memo($from, $to, $filterBy, $filterValue, false);
        }

        $fileName = 'reports/' . $from . "-" . $to . "." . $type;

        return $this->reportGeneration($records, $headers, 'reports.dbp.memo', $fileName, $type);
    }

    // onBoarding
    public function onBoardingList(array $attr) {
        
        $headers = [
            'Account Number',
            'RSBSA Number',
            'First Name',
            'Middle Name',
            'Last Name',
            'Name Extension',
            'Birth Date',
            'City/Municipality',
            'Province State',
            'Profile Status',
            'On Boarded At',
            'Approved Date',
            'Remarks',
        ];
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';
        $filterBy = '';
        $filterValue = '';

        $payload = $this->paramsGeneration($attr);
        $from = $payload['from'];
        $to = $payload['to'];
        $type = $payload['type'];
        $filterBy = $payload['filterBy'];
        $filterValue = $payload['filterValue'];

        $records = [];

        $records = [];
        if($attr && isset($attr['type']) && $attr['type'] == 'API') {
            $records = $this->dbpRepository->onBoardingList($from, $to, $filterBy, $filterValue, true);
        } else {
            $records = $this->dbpRepository->onBoardingList($from, $to, $filterBy, $filterValue, false);
        }

        $fileName = 'reports/' . $from . "-" . $to . "." . $type;

        return $this->reportGeneration($records, $headers, 'reports.dbp.on_boarding', $fileName, $type);
    }

    // transactionHistories
    public function transactionHistories(array $attr) {
        
        $headers = [
            'Transaction Date',
            'Reference Number',
            'RSBSA Number',
            'Account Number',
            'First Name',
            'Middle Name',
            'Last Name',
            'Name',
            'Total Amount',
            'Status',
            'Transaction Type',
            'Current Balance',
            'Available Balance',
        ];
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';
        $filterBy = '';
        $filterValue = '';

        $payload = $this->paramsGeneration($attr);
        $from = $payload['from'];
        $to = $payload['to'];
        $type = $payload['type'];
        $filterBy = $payload['filterBy'];
        $filterValue = $payload['filterValue'];

        $records = [];

        $records = [];
        if($attr && isset($attr['type']) && $attr['type'] == 'API') {
            $records = $this->dbpRepository->transactionHistories($from, $to, $filterBy, $filterValue, true);
        } else {
            $records = $this->dbpRepository->transactionHistories($from, $to, $filterBy, $filterValue, false);
        }

        $fileName = 'reports/' . $from . "-" . $to . "." . $type;

        return $this->reportGeneration($records, $headers, 'reports.dbp.transaction_histories', $fileName, $type);
    }
}
