<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Services\Report\IReportService;
use App\Services\Utilities\PDF\IPDFService;
use App\Services\Transaction\ITransactionService;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\UserTransaction\UserTransactionHistoryRequest;
use App\Http\Requests\TransactionHistory\DownloadTransactionHistoryRequest;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Traits\Errors\WithUserErrors;

class UserTransactionHistoryController extends Controller
{

    use WithUserErrors;
    private IUserTransactionHistoryRepository $userTransactionHistory;
    private ITransactionService $transactionService;
    private IResponseService $responseService;
    private IPDFService $pdfService;

    private IReportService $reportService;


    public function __construct(IResponseService                  $responseService,
                                IUserTransactionHistoryRepository $userTransactionHistory,
                                ITransactionService               $transactionService,
                                IPDFService                       $pdfService,
                                IReportService                    $reportService
                                )
    {
        $this->responseService = $responseService;
        $this->userTransactionHistory = $userTransactionHistory;
        $this->transactionService = $transactionService;
        $this->pdfService = $pdfService;
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $status = 'ALL';
        if ($request->has('status')) {
            $status = $request->status;
        }

        $records = $this->userTransactionHistory->getByAuthUserViaViews($status);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    public function transactionHistoryAdmin(Request $request)
    {
        return $this->reportService->transactionReportAdmin($request->all());
    }

    public function show(string $id)
    {
        $record = $this->userTransactionHistory->findTransactionWithRelationViaView($id);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function download(DownloadTransactionHistoryRequest $request)
    {
        return $data = $this->transactionService->generateTransactionHistory(request()->user()->id, $request->from, $request->to);
    }

    public function countTotalAmountEachUser(DownloadTransactionHistoryRequest $request)
    {
        $record = $this->userTransactionHistory->countTransactionHistoryByDateRangeWithAmountLimitWithPaginate($request->from, $request->to);
        return $this->responseService->successResponse($record->toArray());
    }

    public function downloadCountTotalAmountEachUserPDF(DownloadTransactionHistoryRequest $request)
    {
        $record = $this->userTransactionHistory->countTransactionHistoryByDateRangeWithAmountLimit($request->from, $request->to);
        $data = [
            'datas' => $record,
            'from' => $request->from,
            'to' => $request->to
        ];

        return $this->pdfService->generatePDFNoUserPassword($data, 'reports.user_transaction_history.user_transaction_history');

    }

    public function downloadCountTotalAmountEachUserCSV(DownloadTransactionHistoryRequest $request) {
        return $this->transactionService->downloadCountTotalAmountEachUserCSV($request);
    }

    public function generateTransactionHistory(UserTransactionHistoryRequest $request) {

        // validate by date created
        if(request()->user()) {
            $dateAccountCreated = request()->user()->created_at;
            if($dateAccountCreated) {
                if(Carbon::parse($dateAccountCreated)->greaterThan(Carbon::parse($request->from))) {
                    $this->dateFromBeforeDateCreated(Carbon::parse($dateAccountCreated)->format('F m, Y'));
                }
            }
        }

        $attr = $request->all();
        $attr['auth_user'] = request()->user()->id;
        $record = $this->transactionService->generateTransactionHistoryByEmail($attr);
        return $this->responseService->successResponse([]);
    }
}
