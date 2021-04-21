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


    private $statusActiveError = "Your account is disabled. Please contact Squidpay support.";
    private $emergencyLockError = "Your Account has been locked, Please contact Squidpay Support for assistance in unlocking your account.";
    private $minTierError = "Oops! To completely access all Squidpay services, please update your profile. Thank you.";
    private $monthlyLimitExceedError = "Oh No! You have exceeded your monthly limit.";
    private $balanceNotEnoughError = "Oops! You do not have enough balance.";
    
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
            // IF NOT TIER 1
            if($userTier->account_status != "BASIC") {
                // Stage 4 Checking if total transaction is maxed out
                $doesNotMeetMonthlyTransactionLimit = $this->checkUserMonthlyTransactionLimit($userAccountId, $total_amount);
                // Stage 5 Check if balance is sufficient
                $isBalanceSufficient = $this->checkUserBalance($userAccountId, $total_amount);
                return true;
            }
            // HOLD FOR TIER
            throw ValidationException::withMessages([
                'tier_status' => $this->minTierError
            ]);
        }
        else {
            // NOT CASH IN
            // Stage 4 Checking if total transaction is maxed out
            $doesNotMeetMonthlyTransactionLimit = $this->checkUserMonthlyTransactionLimit($userAccountId, $total_amount);
            // Stage 5 Check if balance is sufficient
            $isBalanceSufficient = $this->checkUserBalance($userAccountId, $total_amount);
            return true;
        }
        return false;
    }


    public function checkUserStatus(string $userAccountId){
        $user_detail = $this->userDetailRepository->getByUserId($userAccountId);
        if($user_detail) {
            if($user_detail->user_account_status == "Active") {
                return true;
            }
            throw ValidationException::withMessages([
                'user_account_status' => $this->statusActiveError
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
                'emergency_lock_status' => $this->emergencyLockError
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

    public function checkUserMonthlyTransactionLimit(string $userAccountId, $totalAmount){
        $tier = $this->tierRepository->getTierByUserAccountId($userAccountId);
        if($tier) {
            $from = Carbon::now()->startOfMonth()->format('Y-m-d');
            $to = Carbon::now()->endOfMonth()->format('Y-m-d');
            $totalTransactionCurrentMonth = $this->userTransactionHistoryRepository->getTotalTransactionAmountByUserAccountIdDateRange($userAccountId, $from, $to);
            $sumUp = $totalTransactionCurrentMonth + $totalAmount;
            if($tier->monthly_limit >= $sumUp) {
                return true;
            }
            throw ValidationException::withMessages([
                'monthly_limit_reached' => $this->monthlyLimitExceedError
            ]);
        }
        throw ValidationException::withMessages([
            'tier_not_found' => 'Tier not found or set'
        ]);
    }

    public function checkUserBalance(string $userAccountId, $totalAmount){
        $balance =  $this->userBalanceRepository->getUserBalanceInfoById($userAccountId, $totalAmount);
        if($balance >= $totalAmount) {
            return true;
        }
        throw ValidationException::withMessages([
            'balance_not_sufficient' => $this->balanceNotEnoughError
        ]);
    }
}
