<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

class TransactionService implements ITransactionService
{
    private IUserBalanceRepository $userBalanceRepository;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    
    public function __construct(IUserBalanceRepository $userBalanceRepository, IUserTransactionHistoryRepository $userTransactionHistoryRepository)
    {
        $this->userBalanceRepository = $userBalanceRepository;        
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
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
        foreach($records as $record) {
            dd($record->toArray());
        }
    }
}
