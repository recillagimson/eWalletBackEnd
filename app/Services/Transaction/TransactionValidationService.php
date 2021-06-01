<?php


namespace App\Services\Transaction;


use App\Enums\AccountTiers;
use App\Models\UserAccount;
use Illuminate\Support\Carbon;
use App\Models\TransactionCategory;
use App\Enums\TransactionCategoryIds;
use App\Repositories\InAddMoney\IInAddMoneyRepository;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithUserErrors;
use App\Repositories\Tier\ITierRepository;
use Illuminate\Validation\ValidationException;
use App\Services\OutBuyLoad\IOutBuyLoadService;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

class TransactionValidationService implements ITransactionValidationService
{
    use WithAuthErrors, WithUserErrors;

    private IUserBalanceRepository $userBalanceRepository;
    private IUserAccountRepository $userAccountRepository;
    private IUserDetailRepository $userDetailRepository;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private ITransactionCategoryRepository $transactionCategoryRepository;
    private ITierRepository $tierRepository;

    // OUT TRANSACTION HISTORY
    private IOutBuyLoadRepository $outBuyLoadRepository;
    private IOutPayBillsRepository $outPayBillsRepository;
    private IOutSend2BankRepository $outsend2BankRepository;
    private IOutSendMoneyRepository $outSendMoneyRepository;

    // ADD TRANSACTION HISTORY
    private IInAddMoneyRepository $addMoneyRepository;
    private IInReceiveMoneyRepository $receiveMoneyRepository;


    public function __construct(IUserBalanceRepository $userBalanceRepository, IUserTransactionHistoryRepository $userTransactionHistoryRepository, IUserAccountRepository $userAccountRepository, IUserDetailRepository $userDetailRepository, ITransactionCategoryRepository $transactionCategoryRepository, ITierRepository $tierRepository, IOutBuyLoadRepository $outBuyLoadRepository, IOutSend2BankRepository $outsend2BankRepository, IOutSendMoneyRepository $outSendMoneyRepository, IOutPayBillsRepository $outPayBillsRepository, IInAddMoneyRepository $addMoneyRepository, IInReceiveMoneyRepository $receiveMoneyRepository)
    {
        $this->userBalanceRepository = $userBalanceRepository;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->transactionCategoryRepository = $transactionCategoryRepository;
        $this->tierRepository = $tierRepository;

        
        $this->outBuyLoadRepository = $outBuyLoadRepository;
        $this->outsend2BankRepository = $outsend2BankRepository;
        $this->outSendMoneyRepository = $outSendMoneyRepository;
        $this->outPayBillsRepository = $outPayBillsRepository;

        $this->addMoneyRepository = $addMoneyRepository;
        $this->receiveMoneyRepository = $receiveMoneyRepository;
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
            if ($user->tier_id !== AccountTiers::tier1) $this->checkUserMonthlyTransactionLimit($user, $totalAmount, $transactionCategoryId);
            $this->userTierInvalid();
        } else {
            // FOR NON-CASHIN TRANSACTIONS
            // Stage 4 Check if balance is sufficient
            $this->checkUserBalance($user, $totalAmount);
            // Stage 5 Checking if total transaction is maxed out
            $this->checkUserMonthlyTransactionLimit($user, $totalAmount, $transactionCategoryId);
        }

    }

    public function validateUser(UserAccount $user)
    {
        if (!$user) $this->accountDoesntExist();
        if (!$user->is_active) $this->accountDeactivated();
        if (!$user->profile) $this->userProfileNotUpdated();
        if (!$user->balanceInfo) $this->userInsufficientBalance();
    }

    public function checkUserMonthlyTransactionLimit(UserAccount $user, float $totalAmount, string $transactionCategoryId, array $customMessage = [])
    {
        $tier = $this->tierRepository->get($user->tier_id);
        if ($tier) {
            $from = Carbon::now()->startOfMonth()->format('Y-m-d');
            $to = Carbon::now()->endOfMonth()->format('Y-m-d');

            $transactionCategory = $this->transactionCategoryRepository->get($transactionCategoryId);

            if($transactionCategory) {
                $totalTransactionCurrentMonth = 0;

                // OUT TRANSACTIONS
                if($transactionCategory->transaction_type === 'NEGATIVE') {
                    $buyLoad = (Double) $this->outBuyLoadRepository->getSumOfTransactions($from, $to);
                    $payBills = (Double) $this->outPayBillsRepository->getSumOfTransactions($from, $to);
                    $send2Banks = (Double) $this->outsend2BankRepository->getSumOfTransactions($from, $to);
                    $sendMoney =  (Double) $this->outSendMoneyRepository->getSumOfTransactions($from, $to);

                    $totalTransactionCurrentMonth = $buyLoad + $payBills + $send2Banks + $sendMoney;
                    
                } else {
                    $addMoneyFromBank = (Double) $this->addMoneyRepository->getSumOfTransactions($from, $to);
                    $receiveMoney = (Double) $this->receiveMoneyRepository->getSumOfTransactions($from, $to);
                    
                    $totalTransactionCurrentMonth = $addMoneyFromBank + $receiveMoney;
                }

                // $totalTransactionCurrentMonth = $this->userTransactionHistoryRepository
                // ->getTotalTransactionAmountByUserAccountIdDateRange($user->id, $from, $to, $transactionCategory);

                $sumUp = $totalTransactionCurrentMonth + $totalAmount;

                if ((Double) $sumUp <= (Double) $tier->monthly_limit) return;

                if(isset($customMessage) && count($customMessage) > 0) {
                    $this->handleCustomErrorMessage($customMessage['key'], $customMessage['value']);
                }

                $this->userMonthlyLimitExceeded();
            }

            return ValidationException::withMessages([
                'transaction_category_not_found' => 'Transaction Category not found'
            ]);
        }

        $this->userTierInvalid();
    }

    public function checkUserBalance(UserAccount $user, $totalAmount)
    {
        $balance = $user->balanceInfo ? $user->balanceInfo : $this->userBalanceRepository->getByUser($user->id);
        if (!$balance || $balance->available_balance < $totalAmount) $this->userInsufficientBalance();
    }
}
