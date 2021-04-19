<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

class TransactionValidationService implements ITransactionValidationService
{
    private IUserBalanceRepository $userBalanceRepository;
    private IUserAccountRepository $userAccountRepository;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    
    public function __construct(IUserBalanceRepository $userBalanceRepository, IUserTransactionHistoryRepository $userTransactionHistoryRepository, IUserAccountRepository $userAccountRepository)
    {
        $this->userBalanceRepository = $userBalanceRepository;        
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->userAccountRepository = $userAccountRepository;
    }

    public function transactionValidation(string $userAccountId, string $transactionCategoryId) {}
    public function checkUserStatus(string $userAccountId){}
    public function checkUserLockStatus(string $userAccountId){}
    public function checkTransaction(string $transactionCategoryId){}
    public function checkUserTier(string $userAccountId){}
    public function checkUserMonthlyTransactionLimit(string $userAccountId){}
    public function checkUserBalance(string $userAccountId){}
}
