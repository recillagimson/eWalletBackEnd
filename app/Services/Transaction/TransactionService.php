<?php


namespace App\Services\Transaction;

use App\Exports\TransactionReport\TransactionReport;
use App\Models\UserAccount;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\AddMoney\UBP\IUbpAddMoneyService;
use App\Services\AddMoneyV2\IAddMoneyService;
use App\Services\BuyLoad\IBuyLoadService;
use App\Services\PayBills\IPayBillsService;
use App\Services\Send2Bank\Pesonet\ISend2BankPesonetService;
use App\Services\ThirdParty\ECPay\IECPayService;
use App\Services\Utilities\CSV\ICSVService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Traits\Errors\WithUserErrors;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class TransactionService implements ITransactionService
{

    use WithUserErrors;

    private IUserBalanceRepository $userBalanceRepository;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private ICSVService $csvService;
    private IPayBillsService $paybillsService;
    private ISend2BankPesonetService $s2bService;
    private IBuyLoadService $buyLoadService;
    private IAddMoneyService $addMoneyService;
    private IUbpAddMoneyService $ubpAddMoneyService;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepo;
    private IEmailService $emailService;
    private IUserDetailRepository $userDetailRepository;
    private IECPayService $ecPayService;

    public function __construct(IUserBalanceRepository            $userBalanceRepository,
                                IUserTransactionHistoryRepository $userTransactionHistoryRepository,
                                ICSVService                       $csvService,
                                IPayBillsService                  $paybillsService,
                                ISend2BankPesonetService          $s2bService,
                                IBuyLoadService                   $buyLoadService,
                                IAddMoneyService                  $addMoneyService,
                                IUbpAddMoneyService               $ubpAddMoneyService,
                                IUserTransactionHistoryRepository $userTransactionHistoryRepo,
                                IEmailService                     $emailService,
                                IUserDetailRepository             $userDetailRepository,
                                IECPayService                     $ecPayService)
    {
        $this->userBalanceRepository = $userBalanceRepository;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->csvService = $csvService;
        $this->paybillsService = $paybillsService;
        $this->s2bService = $s2bService;
        $this->buyLoadService = $buyLoadService;
        $this->addMoneyService = $addMoneyService;
        $this->ubpAddMoneyService = $ubpAddMoneyService;
        $this->userTransactionHistoryRepo = $userTransactionHistoryRepo;
        $this->emailService = $emailService;
        $this->userDetailRepository = $userDetailRepository;
        $this->ecPayService = $ecPayService;
    }

    public function processUserPending(UserAccount $user)
    {
        Log::info('Processing Pending Transactions:', $user->toArray());

        $addMoneyResponse = $this->addMoneyService->processPending($user->id);
        Log::info('Add Money Via DragonPay:', $addMoneyResponse);

        $paybillsResponse = $this->paybillsService->processPending($user);
        Log::info('Pay Bills Process Pending Result:', $paybillsResponse);

        $s2bResponse = $this->s2bService->processPending($user->id);
        Log::info('Send 2 Bank Process Pending Result:', $s2bResponse);

        $buyLoadResponse = $this->buyLoadService->processPending($user->id);
        Log::info('Buy Load Process Pending Result:', $buyLoadResponse);

        $ecPayResponse = $this->ecPayService->batchConfirmPayment($user->id);
        Log::info('ECPay Add Money Process Pending Result:', $ecPayResponse);
    }

    public function processAllPending()
    {
        $this->s2bService->processAllPending();
        $this->addMoneyService->processAllPending();
        $this->paybillsService->processAllPending();
        $this->buyLoadService->processAllPending();
    }

    public function addUserBalanceInfo(string $userAccountId, string $currencyId, float $availableBalance, float $pendingBalance)
    {
        return $this->userBalanceRepository->create([
            'user_account_id' => $userAccountId,
            'currency_id' => $currencyId,
            'available_balance' => $availableBalance,
            'pending_balance' => $pendingBalance,
            'user_created' => request()->user()->id,
            'user_updated' => request()->user()->id,
        ]);
    }

    // USER TRANSACTION HISTORY
    public function createUserTransactionEntry(string $userAccountId, string $transactionId, string $referenceNumber, string $transactionCategoryId) {
        return $this->userTransactionHistoryRepository->create([
            'user_account_id' => $userAccountId,
            'transaction_id' => $transactionId,
            'reference_number' => $referenceNumber,
            'transaction_category_id' => $transactionCategoryId,
            'user_created' => request()->user()->id,
            'user_updated' => request()->user()->id,
        ]);
    }

    public function createUserTransactionEntryUnauthenticated(string $userAccountId, string $transactionId, string $referenceNumber, float $total_amount, string $transactionCategoryId) {
        return $this->userTransactionHistoryRepository->create([
            'user_account_id' => $userAccountId,
            'transaction_id' => $transactionId,
            'reference_number' => $referenceNumber,
            'total_amount' => $total_amount,
            'transaction_category_id' => $transactionCategoryId,
            'user_created' => $userAccountId,
            'user_updated' => $userAccountId,
        ]);
    }

    public function generateTransactionHistory(string $userAccountId, string $dateFrom, string $dateTo) {
        $records = $this->userTransactionHistoryRepository->getTransactionHistoryByIdAndDateRange($userAccountId, $dateFrom, $dateTo);
        $data = [
            'records' => $records,
            'from' => $dateFrom,
            'to' => $dateTo,
        ];
        $password = str_replace(" ", "", strtolower(request()->user()->profile->last_name)) . Carbon::parse(request()->user()->profile->birth_date)->format('mdY');
        $file_name = request()->user()->profile->first_name . "_" . request()->user()->profile->last_name . "_" . $dateFrom . "_" . $dateTo . '.pdf';
        \Log::info($password);
        $pdf = PDF::loadView('reports.transaction_history.transaction_history', $data);
        $pdf->SetProtection(['copy', 'print'], $password, 'squidP@y');
        return $pdf->stream($file_name);
    }

    public function downloadCountTotalAmountEachUserCSV(object $request)
    {
        $records = $this->userTransactionHistoryRepository->countTransactionHistoryByDateRangeWithAmountLimit($request->from, $request->to);
        $columns = array('Customer Account ID', 'Date of Transaction', 'Amount');
        $datas = [];

        foreach ($records as $record) {
            array_push($datas, [
                'Customer Account ID' => $record->user_account_id,
                'Date of Transaction' => Carbon::parse($record->transaction_date)->format('F d, Y g:i A'),
                'Amount' => $record->amount,
            ]);
        }

        return $this->csvService->generateCSV($datas, $columns);
    }

    public function getTransactionHistoryAdmin(array $attr, bool $paginated = true) {
        $records = $this->userTransactionHistoryRepository->getTransactionHistoryAdmin($attr, false);
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->subDays(30)->format('Y-m-d');
        $type = 'API';

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
            return [
                'temp_url' => $temp_url
            ];
        } else if($type === 'XLSX') {
            Excel::store(new TransactionReport($records, $type, $from, $to), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX);
            $temp_url = $this->s3TempUrl($fileName);
            return [
                'temp_url' => $temp_url
            ];
        } else {
            return $records->toArray();
        }
    }

    public function s3TempUrl(string $generated_link) {
        $temp_url = Storage::disk('s3')->temporaryUrl($generated_link, Carbon::now()->addMinutes(30));
        return $temp_url;
    }

    public function generateTransactionHistoryByEmail(array $attr) {

        // validate by date created
        if(request()->user()) {
            $dateAccountCreated = Carbon::parse(Carbon::parse(request()->user()->created_at)->format('Y-m-d'));
            if($dateAccountCreated) {
                // BEFORE DATE CREATED VALIDATION
                if(Carbon::parse($dateAccountCreated)->greaterThan(Carbon::parse($attr['from']))) {
                    $this->dateFromBeforeDateCreated($dateAccountCreated->format('F d, Y'));
                }

                if(Carbon::parse($dateAccountCreated)->greaterThan(Carbon::parse($attr['to']))) {
                    $this->dateToBeforeDateCreated($dateAccountCreated->format('F d, Y'));
                }

                // MUST BE LESS THAN TODAY
                if(Carbon::parse($attr['from'])->greaterThan(Carbon::now())  && Carbon::parse($dateAccountCreated)->lessThan(Carbon::parse($attr['from']))) {
                    $this->dateFromBeforeDateToday(Carbon::now()->format('F d, Y'));
                }

                if(Carbon::parse($attr['to'])->greaterThan(Carbon::now()) && Carbon::parse($dateAccountCreated)->lessThan(Carbon::parse($attr['to']))) {
                    $this->dateToBeforeDateToday(Carbon::now()->format('F d, Y'));
                }

            }
        }

        $records = $this->userTransactionHistoryRepo->getFilteredTransactionHistory($attr['auth_user'], $attr['from'], $attr['to']);

        $fileName = $attr['from'] . "-" . $attr['to'] . ".pdf";
        $user = $this->userDetailRepository->getByUserId($attr['auth_user']);
        if(!$user) {
            $this->userProfileNotUpdated();
        }
        $password = Carbon::parse($user->birth_date)->format('mdY');
        $this->emailService->sendUserTransactionHistory($attr['email'], $records->toArray(), $fileName, $user->first_name, $attr['from'], $attr['to'], $password);
        return;
    }
}
