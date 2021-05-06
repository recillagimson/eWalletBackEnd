<?php


namespace App\Services\Transaction;


use App\Enums\AccountTiers;
use App\Enums\TransactionCategoryIds;
use App\Models\UserAccount;
use App\Repositories\Tier\ITierRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Support\Carbon;

class TransactionValidationService implements ITransactionValidationService
{
    use WithAuthErrors, WithUserErrors;

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

    public function validate(UserAccount $user, string $transactionCategoryId, float $totalAmount)
    {
        // Stage 1 Check Account Status
        // Stage 2 Check if Locked out
        $this->validateUser($user);

        // Stage 3 Get Transaction Category
        $cashin = in_array($transactionCategoryId, TransactionCategoryIds::cashinTransactions);
        // Check if Cash in
        // DragonPay or POS
        // POS POSADDFUNDS
        // DRAGONPAY CASHINDRAGONPAY
        if ($cashin) {
            // FOR CASH IN TRANSACTIONS
            // MUST BE TIER 2 AND ABOVE
            // Stage 5 Checking if total transaction is maxed out
            if ($user->tier_id !== AccountTiers::tier1) $this->checkUserMonthlyTransactionLimit($user, $totalAmount);
            $this->userTierInvalid();
        } else {
            // FOR NON-CASHIN TRANSACTIONS
            // Stage 4 Check if balance is sufficient
            $this->checkUserBalance($user, $totalAmount);
            // Stage 5 Checking if total transaction is maxed out
            $this->checkUserMonthlyTransactionLimit($user, $totalAmount);
        }

    }

    public function validateUser(UserAccount $user)
    {
        if (!$user) $this->accountDoesntExist();
        if (!$user->is_active) $this->accountDeactivated();
        if (!$user->profile) $this->userProfileNotUpdated();
        if (!$user->balanceInfo) $this->userInsufficientBalance();
    }

    public function checkUserMonthlyTransactionLimit(UserAccount $user, float $totalAmount)
    {
        $tier = $this->tierRepository->get($user->tier_id);
        if ($tier) {
            $from = Carbon::now()->startOfMonth()->format('Y-m-d');
            $to = Carbon::now()->endOfMonth()->format('Y-m-d');

            $totalTransactionCurrentMonth = $this->userTransactionHistoryRepository
                ->getTotalTransactionAmountByUserAccountIdDateRange($user->id, $from, $to);

            $sumUp = $totalTransactionCurrentMonth + $totalAmount;
            if ($tier->monthly_limit >= $sumUp) return;
            $this->userMonthlyLimitExceeded();
        }

        $this->userTierInvalid();
    }

    public function checkUserBalance(UserAccount $user, $totalAmount)
    {
        $balance = $user->balanceInfo ? $user->balanceInfo : $this->userBalanceRepository->getByUser($user->id);
        if (!$balance || $balance->available_balance < $totalAmount) $this->userInsufficientBalance();
    }
}
