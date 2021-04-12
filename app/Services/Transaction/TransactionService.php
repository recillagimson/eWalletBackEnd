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
    public function addAvailableBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
        $this->addUserBalanceInfo($user_account_id, $current_id, $available_balance, $pending_balance);
    }
    public function subtractAvailableBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
        $this->addUserBalanceInfo($user_account_id, $current_id, $available_balance, $pending_balance);
    }
    public function addPendingBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
        $this->addUserBalanceInfo($user_account_id, $current_id, $available_balance, $pending_balance);
    }
    public function subtractPendingBalance(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
        $this->addUserBalanceInfo($user_account_id, $current_id, $available_balance, $pending_balance);
    }

    private function addUserBalanceInfo(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
        $record =  $this->userBalanceRepository->create([
            'user_account_id' => $user_account_id,
            'currency_id' => $current_id,
            'available_balance' => $available_balance,
            'pending_balance' => $pending_balance,
            'user_created' => request()->user()->id,
            'user_updated' => request()->user()->id,
        ]);

        return $record;
    }

    // USER TRANSACTION HISTORY
    public function createUserTransactionEntry(string $user_account_id, string $transaction_id, string $reference_number, string $transaction_category_id) {
        $record = $this->userTransactionHistoryRepository->create([
            'user_account_id' => $user_account_id,
            'transaction_id' => $transaction_id,
            'reference_number' => $reference_number,
            'transaction_category_id' => $transaction_category_id,
            'user_created' => request()->user()->id,
            'user_updated' => request()->user()->id,
        ]);
        return $record;
    }
}
