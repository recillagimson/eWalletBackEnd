<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\TransactionHistory\DownloadTransactionHistoryRequest;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Transaction\ITransactionService;
use App\Services\Utilities\PDF\IPDFService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\Request;

class UserTransactionHistoryController extends Controller
{
    private IUserTransactionHistoryRepository $userTransactionHistory;
    private ITransactionService $transactionService;
    private IResponseService $responseService;
    private IPDFService $pdfService;


    public function __construct(IResponseService                  $responseService,
                                IUserTransactionHistoryRepository $userTransactionHistory,
                                ITransactionService               $transactionService,
                                IPDFService                       $pdfService)
    {
        $this->responseService = $responseService;
        $this->userTransactionHistory = $userTransactionHistory;
        $this->transactionService = $transactionService;
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        $status = 'ALL';
        if ($request->has('status')) {
            $status = $request->status;
        }
//        $records = $this->userTransactionHistory->getByAuthUserViaViews($status);
        $records = $this->userTransactionHistory->getByAuthUserViaViews($status);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    public function transactionHistoryAdmin(Request $request)
    {
        $records = $this->userTransactionHistory->getTransactionHistoryAdmin($request->all());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    public function show(string $id)
    {
//        $record = $this->userTransactionHistory->findTransactionWithRelationViaView($id);
        $record = $this->userTransactionHistory->findTransactionWithRelation($id);
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
}
