<?php


namespace App\Services\Transaction;

use App\Models\UserAccount;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\AddMoneyV2\IAddMoneyService;
use App\Services\BuyLoad\IBuyLoadService;
use App\Services\PayBills\IPayBillsService;
use App\Services\Send2Bank\Pesonet\ISend2BankPesonetService;
use App\Services\Utilities\CSV\ICSVService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PDF;

class TransactionService implements ITransactionService
{
    private IUserBalanceRepository $userBalanceRepository;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private ICSVService $csvService;
    private IPayBillsService $paybillsService;
    private ISend2BankPesonetService $s2bService;
    private IBuyLoadService $buyLoadService;
    private IAddMoneyService $addMoneyService;

    public function __construct(IUserBalanceRepository $userBalanceRepository,
                                IUserTransactionHistoryRepository $userTransactionHistoryRepository,
                                ICSVService $csvService,
                                IPayBillsService $paybillsService,
                                ISend2BankPesonetService $s2bService,
                                IBuyLoadService $buyLoadService,
                                IAddMoneyService $addMoneyService)
    {
        $this->userBalanceRepository = $userBalanceRepository;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->csvService = $csvService;
        $this->paybillsService = $paybillsService;
        $this->s2bService = $s2bService;
        $this->buyLoadService = $buyLoadService;
        $this->addMoneyService = $addMoneyService;
    }

    public function processUserPending(UserAccount $user)
    {
        Log::info('Processing Pending Transactions:', $user->toArray());

        //$addMoneyResponse = $this->addMoneyService->processPending($user->id);
        //Log::info('Add Money Via DragonPay:', $addMoneyResponse);

        $paybillsResponse = $this->paybillsService->processPending($user);
        Log::info('Pay Bills Process Pending Result:', $paybillsResponse);

        $s2bResponse = $this->s2bService->processPending($user->id);
        Log::info('Send 2 Bank Process Pending Result:', $s2bResponse);

        $buyLoadResponse = $this->buyLoadService->processPending($user->id);
        Log::info('Buy Load Process Pending Result:', $buyLoadResponse);
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


}
