<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;
use App\Repositories\Tier\ITierRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalance\IUserBalanceRepository;

use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use Illuminate\Validation\ValidationException;

class TransactionValidationService implements ITransactionValidationService
{
    private IUserBalanceRepository $userBalanceRepository;
    private IUserAccountRepository $userAccountRepository;
    private IUserDetailRepository $userDetailRepository;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private ITransactionCategoryRepository $transactionCategoryRepository;
    private ITierRepository $tierRepository;
    
    public function __construct(IUserBalanceRepository $userBalanceRepository, IUserTransactionHistoryRepository $userTransactionHistoryRepository, IUserAccountRepository $userAccountRepository, IUserDetailRepository $userDetailRepository, ITransactionCategoryRepository $transactionCategoryRepository, ITierRepository $tierRepository)
    {
        $this->userBalanceRepository = $userBalanceRepository;        
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->transactionCategoryRepository = $transactionCategoryRepository;
        $this->tierRepository = $tierRepository;
    }

    public function transactionValidation(string $userAccountId, string $transactionCategoryId, $total_amount) {
        // Stage 1 Check Account Status
        $isUserActive = $this->checkUserStatus($userAccountId);
        // Stage 2 Check if Emergency Locked
        $notUserAccountLocked = $this->checkUserLockStatus($userAccountId);
        // Stage 3 Get Transaction Category
        $transactionCategory = $this->getTransactionCategory($transactionCategoryId);
        // Check if Cash in
        // DragonPay or POS 
        // POS POSADDFUNDS
        // DRAGONPAY CASHINDRAGONPAY
        if($transactionCategory && ($transactionCategory->name == "POSADDFUNDS" || $transactionCategory->name == "CASHINDRAGONPAY")) {
            // CASH IN
            $userTier = $this->tierRepository->getTierByUserAccountId($userAccountId);
            // HOLD FOR TIER
        }
        else {
            // NOT CASH IN
        }
    }


    public function checkUserStatus(string $userAccountId){
        $user_detail = $this->userDetailRepository->getByUserId($userAccountId);
        if($user_detail) {
            if($user_detail->user_account_status == "Active") {
                return true;
            }
            throw ValidationException::withMessages([
                'user_account_status' => 'Account Status is not active'
            ]);
        }
        throw ValidationException::withMessages([
            'user_details' => 'Account Details not found'
        ]);
    }

    public function checkUserLockStatus(string $userAccountId){
        $user_detail = $this->userDetailRepository->getByUserId($userAccountId);
        if($user_detail) {
            if($user_detail->emergency_lock_status == "false") {
                return true;
            }
            throw ValidationException::withMessages([
                'emergency_lock_status' => 'Account Emergency Lock is active'
            ]);
        }
        throw ValidationException::withMessages([
            'user_details' => 'Account Details not found'
        ]);
    }

    public function getTransactionCategory(string $transactionCategoryId){
        $transactionCategory = $this->transactionCategoryRepository->getById($transactionCategoryId);
        if($transactionCategory) {
            return $transactionCategory;
        }
        throw ValidationException::withMessages([
            'transaction_category' => 'Transaction Category not found'
        ]);
    }

    public function checkUserTier(string $userAccountId){}
    public function checkUserMonthlyTransactionLimit(string $userAccountId){}
    public function checkUserBalance(string $userAccountId){}
}
