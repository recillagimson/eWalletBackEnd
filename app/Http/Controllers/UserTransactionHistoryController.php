<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Services\Transaction\ITransactionService;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\TransactionHistory\DownloadTransactionHistoryRequest;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\PDF\IPDFService;

class UserTransactionHistoryController extends Controller
{
    private IUserTransactionHistoryRepository $userTransactionHistory;
    private ITransactionService $transactionService;
    private IResponseService $responseService;
    private IPDFService $pdfService;


    public function __construct(IResponseService $responseService,
        IUserTransactionHistoryRepository $userTransactionHistory, 
        ITransactionService $transactionService,
        IPDFService $pdfService)
    {
        $this->responseService = $responseService;
        $this->userTransactionHistory = $userTransactionHistory;
        $this->transactionService = $transactionService;
        $this->pdfService = $pdfService;
    }

    public function index() {
        $records = $this->userTransactionHistory->getByAuthUser();
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    public function show(string $id) {
        $record = $this->userTransactionHistory->findTransactionWithRelation($id);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function download(DownloadTransactionHistoryRequest $request) {
        return $data = $this->transactionService->generateTransactionHistory(request()->user()->id, $request->from, $request->to);
    }

    public function countTotalAmountEachUser(DownloadTransactionHistoryRequest $request) {
        $record = $this->userTransactionHistory->countTransactionHistoryByDateRangeWithAmountLimitWithPaginate($request->from, $request->to);
        return $this->responseService->successResponse($record->toArray());
    }

    public function downloadCountTotalAmountEachUserPDF(DownloadTransactionHistoryRequest $request) {
        $record = $this->userTransactionHistory->countTransactionHistoryByDateRangeWithAmountLimit($request->from, $request->to);
        $file_name = request()->user()->profile->first_name . "_" . request()->user()->profile->last_name;
        $data = [
            'datas' => $record,
            'from'=> $request->from,
            'to'=> $request->to
        ]; 

        return $this->pdfService->generatePDFNoUserPassword($data, $file_name, 'reports.user_transaction_history.user_transaction_history');

    }
}
