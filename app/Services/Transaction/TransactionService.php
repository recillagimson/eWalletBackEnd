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

    public function addUserBalanceInfo(string $userAccountId, string $currentId, float $availableBalance, float $pendingBalance) {
        $record =  $this->userBalanceRepository->create([
            'user_account_id' => $userAccountId,
            'currency_id' => $currentId,
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

    // GET BALANCE INFO
    public function getUserBalanceById(string $userAccountId) {
        return $this->userBalanceRepository->getUserBalanceInfoById($userAccountId);
    }
}
