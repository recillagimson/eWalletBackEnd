<?php

namespace App\Http\Controllers\DBP;

use App\Http\Controllers\Controller;
use App\Services\DBPReport\IDBPReportService;
use Illuminate\Http\Request;

class DBPReportController extends Controller
{
    private IDBPReportService $dbpReportService;
    public function __construct(IDBPReportService $dbpReportService) {
        $this->dbpReportService = $dbpReportService;
    }

    public function customerList(Request $request) {
        return $this->dbpReportService->customerList($request->all());
    }

    public function disbursement(Request $request) {
        return $this->dbpReportService->disbursement($request->all());
    }

    public function memo(Request $request) {
        return $this->dbpReportService->memo($request->all());
    }
    
    public function onBoarding(Request $request) {
        return $this->dbpReportService->onBoardingList($request->all());
    }

    public function transactionHistories(Request $request) {
        return $this->dbpReportService->transactionHistories($request->all());
    }

    public function claims(Request $request) {
        return $this->dbpReportService->claims($request->all());
    }
}
