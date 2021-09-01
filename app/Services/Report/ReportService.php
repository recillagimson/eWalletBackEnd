<?php

namespace App\Services\Report;

use Carbon\Carbon;
use App\Enums\SuccessMessages;
use App\Exports\DRCR\DRCRReport;
use App\Exports\Biller\BillerReport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\User\FarmerListExport;
use Illuminate\Support\Facades\Storage;
use App\Exports\User\CustomerListExport;
use App\Services\Utilities\PDF\IPDFService;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Exports\TransactionReport\TransactionReport;
use App\Services\Utilities\Responses\IResponseService;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Exports\TransactionReport\TransactionReportAdmin;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

class ReportService implements IReportService
{

    private IOutPayBillsRepository $payBills;
    private IPDFService $pdfService;
    private IResponseService $responseService;
    private IDrcrMemoRepository $drcrRepo;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private IUserDetailRepository $userDetail;
    private IUserAccountRepository $userAccount;

    public function __construct(IOutPayBillsRepository $payBills, IPDFService $pdfService, IResponseService $responseService, IDrcrMemoRepository $drcrRepo, IUserTransactionHistoryRepository $userTransactionHistoryRepository, IUserDetailRepository $userDetail, IUserAccountRepository $userAccount)
    {
        $this->payBills = $payBills;
        $this->pdfService = $pdfService;
        $this->responseService = $responseService;
        $this->drcrRepo = $drcrRepo;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->userDetail = $userDetail;
        $this->userAccount = $userAccount;
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

        if($params && isset($params['filter_by']) && isset($params['filter_value'])) {
            $filterBy = $params['filter_by'];
            $filterValue = $params['filter_value'];
        }


        $records = $this->payBills->reportData($from, $to, $filterBy, $filterValue);
        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        if($params['type'] == 'PDF') {

            Excel::store(new BillerReport($records, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::MPDF);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } 
        else if($params['type'] == 'CSV') {
            Excel::store(new BillerReport($records, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } 
        else if($params['type'] == 'API')  {
            return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
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

    public function drcrmemofarmers(array $params) {
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

        if($params && isset($params['filter_by']) && isset($params['filter_value'])) {
            $filterBy = $params['filter_by'];
            $filterValue = $params['filter_value'];
        }

        $records = $this->drcrRepo->reportDataFarmers($from, $to, $filterBy, $filterValue, $type);
        $fileName = 'reports/' . $from . "-" . $to . "." . $type;

        if($params['type'] == 'CSV') {
            Excel::store(new DRCRReport($records, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } 
        else if($params['type'] == 'API')  {
            return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
        }
        else {
            Excel::store(new DRCRReport($records, $params['from'], $params['to'], $params), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        }

    }

    public function transactionReportFarmers(array $attr) {
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';
        $records = $this->userTransactionHistoryRepository->getTransactionHistoryAdminFarmer($attr, false);

        if($attr && isset($attr['from']) && isset($attr['to'])) {
            $from = $attr['from'];
            $to = $attr['to'];
        }
        if($attr && isset($attr['type'])) {
            $type = $attr['type'];
        }
        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        if($type === 'CSV') {
            Excel::store(new TransactionReport($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);

            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } else if($type === 'XLSX') {
            Excel::store(new TransactionReport($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);

        } else {
            // return $records->toArray();
            return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
        }
    }

    public function farmersList(array $attr) {
        $from = Carbon::now()->subDays(30)->format('Y-m-d H:i:s');
        $to = Carbon::now()->format('Y-m-d H:i:s');
        $filterBy = '';
        $filterValue = '';
        $type = 'XLSX';
        
        if($attr && isset($attr['type'])) {
            $type = $attr['type'];
        }
        
        if($attr && isset($attr['from']) && isset($attr['to'])) {
            $from = $attr['from'];
            $to = $attr['to'];
        }
        
        if($attr && isset($attr['filter_by']) && isset($attr['filter_value'])) {
            $filterBy = $attr['filter_by'];
            $filterValue = $attr['filter_value'];
        }

        $records = $this->userDetail->getFarmers($from, $to, $filterBy, $filterValue, $type);

        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        if($type === 'CSV') {
            Excel::store(new FarmerListExport($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);

            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } else if($type === 'XLSX') {
            Excel::store(new FarmerListExport($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);

        } else {
            // return $records->toArray();
            return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
        }
    }

    public function transactionReportAdmin(array $attr) {
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';

        $records = [];
        if($attr && isset($attr['type']) && $attr['type'] == 'API') {
            $records = $this->userTransactionHistoryRepository->getTransactionHistoryAdmin($attr, true);
        } else {
            $records = $this->userTransactionHistoryRepository->getTransactionHistoryAdmin($attr, false);
        }

        if($attr && isset($attr['from']) && isset($attr['to'])) {
            $from = $attr['from'];
            $to = $attr['to'];
        }
        if($attr && isset($attr['type'])) {
            $type = $attr['type'];
        }
        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        if($type === 'CSV') {
            Excel::store(new TransactionReportAdmin($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);

            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } else if($type === 'XLSX') {
            Excel::store(new TransactionReportAdmin($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);

        } else {
            // return $records->toArray();
            return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
        }
    }

    public function customerList(array $attr) {
        
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';

        $records = [];
        if($attr && isset($attr['type']) && $attr['type'] == 'API') {
            $records = $this->userAccount->getAllUsersPaginated($attr, 15, true);
        } else {
            $records = $this->userAccount->getAllUsersPaginated($attr, 15, false);
        }

        if($attr && isset($attr['from']) && isset($attr['to'])) {
            $from = $attr['from'];
            $to = $attr['to'];
        }
        if($attr && isset($attr['type'])) {
            $type = $attr['type'];
        }
        $fileName = 'reports/' . $from . "-" . $to . "." . $type;
        if($type === 'CSV') {
            Excel::store(new CustomerListExport($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::CSV);
            $temp_url = $this->s3TempUrl($fileName);

            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);
        } else if($type === 'XLSX') {
            Excel::store(new CustomerListExport($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return $this->responseService->successResponse(['temp_url' => $temp_url], SuccessMessages::success);

        } else {
            // return $records->toArray();
            return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
        }
    }
}
