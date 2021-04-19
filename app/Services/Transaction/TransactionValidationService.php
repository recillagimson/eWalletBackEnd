<?php


namespace App\Services\Transaction;


use App\Models\UserAccount;
use Illuminate\Support\Carbon;
use App\Repositories\Tier\ITierRepository;
use Illuminate\Validation\ValidationException;
use App\Repositories\UserAccount\IUserAccountRepository;

use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

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
        $doesNotMeetMonthlyTransactionLimit = true;
        if($transactionCategory && ($transactionCategory->name == "POSADDFUNDS" || $transactionCategory->name == "CASHINDRAGONPAY")) {
            // CASH IN
            $userTier = $this->tierRepository->getTierByUserAccountId($userAccountId);
            // HOLD FOR TIER
        }
        else {
            // NOT CASH IN
            $doesNotMeetMonthlyTransactionLimit = $this->checkUserMonthlyTransactionLimit($userAccountId, $total_amount);
            dd($doesNotMeetMonthlyTransactionLimit);
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

    public function checkUserMonthlyTransactionLimit(string $userAccountId, $total_amount){
        $tier = $this->tierRepository->getTierByUserAccountId($userAccountId);
        if($tier) {
            $from = Carbon::now()->startOfMonth()->format('Y-m-d');
            $to = Carbon::now()->endOfMonth()->format('Y-m-d');
            $total_transaction_current_month = $this->userTransactionHistoryRepository->getTotalTransactionAmountByUserAccountIdDateRange($userAccountId, $from, $to);
            $sum_up = $total_transaction_current_month + $total_amount;
            if($tier->monthly_limit >= $sum_up) {
                return true;
            }
            throw ValidationException::withMessages([
                'monthly_limit_reached' => 'Monthly Limit Reached'
            ]);
        }
        throw ValidationException::withMessages([
            'tier_not_found' => 'Tier not found or set'
        ]);
    }

    public function checkUserBalance(string $userAccountId){}
}
