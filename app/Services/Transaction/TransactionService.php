<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;
use App\Repositories\UserBalance\IUserBalanceRepository;

class TransactionService implements ITransactionService
{
    private IUserBalanceRepository $userBalanceRepository;
    
    public function __construct(IUserBalanceRepository $userBalanceRepository)
    {
        $this->userBalanceRepository = $userBalanceRepository;        
    }
    
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

    public function addUserBalanceInfo(string $user_account_id, string $current_id, float $available_balance, float $pending_balance) {
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
}
