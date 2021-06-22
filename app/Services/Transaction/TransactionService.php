<?php


namespace App\Services\Transaction;

use PDF;
use Carbon\Carbon;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\CSV\ICSVService;

class TransactionService implements ITransactionService
{
    private IUserBalanceRepository $userBalanceRepository;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private ICSVService $csvService;
    
    public function __construct(IUserBalanceRepository $userBalanceRepository, IUserTransactionHistoryRepository $userTransactionHistoryRepository,
                                ICSVService $csvService)
    {
        $this->userBalanceRepository = $userBalanceRepository;        
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->csvService = $csvService;
    }
    
    // FOR USER BALANCE INFO
    // public function addAvailableBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
    //     $this->addUserBalanceInfo($user_account_id, $current_id, $available_balance, $pending_balance);
    // }
    // public function subtractAvailableBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
    //     $this->addUserBalanceInfo($user_account_id, $current_id, $available_balance, $pending_balance);
    // }
    // public function addPendingBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
    //     $this->addUserBalanceInfo($user_account_id, $current_id, $available_balance, $pending_balance);
    // }
    // public function subtractPendingBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
    //     $this->addUserBalanceInfo($user_account_id, $current_id, $available_balance, $pending_balance);
    // }

    public function addUserBalanceInfo(string $userAccountId, string $currencyId, float $availableBalance, float $pendingBalance) {
        $record =  $this->userBalanceRepository->create([
            'user_account_id' => $userAccountId,
            'currency_id' => $currencyId,
            'available_balance' => $availableBalance,
            'pending_balance' => $pendingBalance,
            'user_created' => request()->user()->id,
            'user_updated' => request()->user()->id,
        ]);

        return $record;
    }

    // USER TRANSACTION HISTORY
    public function createUserTransactionEntry(string $userAccountId, string $transactionId, string $referenceNumber, string $transactionCategoryId) {
        $record = $this->userTransactionHistoryRepository->create([
            'user_account_id' => $userAccountId,
            'transaction_id' => $transactionId,
            'reference_number' => $referenceNumber,
            'transaction_category_id' => $transactionCategoryId,
            'user_created' => request()->user()->id,
            'user_updated' => request()->user()->id,
        ]);
        return $record;
    }

    public function createUserTransactionEntryUnauthenticated(string $userAccountId, string $transactionId, string $referenceNumber, float $total_amount, string $transactionCategoryId) {
        $record = $this->userTransactionHistoryRepository->create([
            'user_account_id' => $userAccountId,
            'transaction_id' => $transactionId,
            'reference_number' => $referenceNumber,
            'total_amount' => $total_amount,
            'transaction_category_id' => $transactionCategoryId,
            'user_created' => $userAccountId,
            'user_updated' => $userAccountId,
        ]);
        return $record;
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
        $file_name = request()->user()->profile->first_name . "_" . request()->user()->profile->last_name;
        $columns = array('Customer Account ID', 'Date of Transaction', 'Amount');
        $datas = [];

        foreach ($records as $record) {
            array_push($datas, [
                'Customer Account ID'  => $record->user_account_id,
                'Date of Transaction'  => Carbon::parse($record->transaction_date)->format('F d, Y G:i A'),
                'Amount'  => $record->amount,
            ]);
        }

        return $this->csvService->generateCSV($datas, $file_name, $columns);
    }
}
