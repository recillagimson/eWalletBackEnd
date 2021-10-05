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
        $result = $this->dbpReportService->customerList($request->all());
    }
}
