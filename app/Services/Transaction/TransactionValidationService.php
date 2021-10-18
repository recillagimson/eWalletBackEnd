<?php


namespace App\Services\Transaction;


use App\Enums\TransactionCategoryIds;
use App\Models\UserAccount;
use App\Repositories\InAddMoney\IInAddMoneyRepository;
use App\Repositories\InAddMoneyBPI\IInAddMoneyBPIRepository;
use App\Repositories\InAddMoneyEcPay\IInAddMoneyEcPayRepository;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\Tier\ITierRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
    private IInAddMoneyBPIRepository $addMoneyBPIRepository;
    private IInAddMoneyEcPayRepository $addMoneyEcPayRepository;


    public function __construct(IUserBalanceRepository $userBalanceRepository, IUserTransactionHistoryRepository $userTransactionHistoryRepository, IUserAccountRepository $userAccountRepository, IUserDetailRepository $userDetailRepository, ITransactionCategoryRepository $transactionCategoryRepository, ITierRepository $tierRepository, IOutBuyLoadRepository $outBuyLoadRepository, IOutSend2BankRepository $outsend2BankRepository, IOutSendMoneyRepository $outSendMoneyRepository, IOutPayBillsRepository $outPayBillsRepository, IInAddMoneyRepository $addMoneyRepository, IInReceiveMoneyRepository $receiveMoneyRepository,
    IInAddMoneyBPIRepository $iInAddMoneyBPIRepository, IInAddMoneyEcPayRepository $addMoneyEcPayRepository
    )
    {
        $this->userBalanceRepository = $userBalanceRepository;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->transactionCategoryRepository = $transactionCategoryRepository;
        $this->tierRepository = $tierRepository;
        $this->iInAddMoneyBPIRepository = $iInAddMoneyBPIRepository;
        $this->addMoneyEcPayRepository = $addMoneyEcPayRepository;


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
            // Stage 5 Checking if total transaction is maxed out
            $this->checkUserMonthlyTransactionLimit($user, $totalAmount, $transactionCategoryId);
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
                $sumUp = 0;

                //IN TRANSACTIONS
                if($transactionCategory->transaction_type === 'POSITIVE') {
                    $addMoneyFromBank = (Double) $this->addMoneyRepository->getSumOfTransactions($from, $to, $user->id);
                    $receiveMoney = (double)$this->receiveMoneyRepository->getSumOfTransactions($from, $to, $user->id);
                    $bpiAddMoney = (double)$this->iInAddMoneyBPIRepository->getSumOfTransactions($from, $to, $user->id);
                    $ecpayAddMoney = (double)$this->addMoneyEcPayRepository->getSumOfTransactions($from, $to, $user->id);
                    $sumUp = $addMoneyFromBank + $receiveMoney + $bpiAddMoney + $ecpayAddMoney;

                    //$buyLoad = (Double) $this->outBuyLoadRepository->getSumOfTransactions($from, $to, $user->id);
                    //$payBills = (Double) $this->outPayBillsRepository->getSumOfTransactions($from, $to, $user->id);
                    // $send2Banks = (Double) $this->outsend2BankRepository->getSumOfTransactions($from, $to, $user->id);
                    //$sendMoney =  (Double) $this->outSendMoneyRepository->getSumOfTransactions($from, $to, $user->id);

                    //$totalTransactionCurrentMonth = $buyLoad+$payBills+ $send2Banks+$sendMoney;
                    // $sumUp = $totalTransactionCurrentMonth + $totalAmount;

                } //else {
                    //$addMoneyFromBank = (Double) $this->addMoneyRepository->getSumOfTransactions($from, $to, $user->id);

                //$receiveMoney = (Double) $this->receiveMoneyRepository->getSumOfTransactions($from, $to, $user->id);
                   // $in = $addMoneyFromBank + $receiveMoney;
                    //$totalTransactionCurrentMonth =0;

                 //}

                // $totalTransactionCurrentMonth = $this->userTransactionHistoryRepository
                // ->getTotalTransactionAmountByUserAccountIdDateRange($user->id, $from, $to, $transactionCategory);


                if ((double)$sumUp <= (double)$tier->monthly_limit) return;

                if (isset($customMessage) && count($customMessage) > 0) {
                    $this->handleCustomErrorMessage($customMessage['key'], $customMessage['value']);
                }

                Log::error('Account Monthly Limit Exceeded:', [
                    'totalFrom' => $from,
                    'totalTo' => $to,
                    'totalMonthlyAmount' => $sumUp,
                    'tierMonthlyLimit' => $tier->monthly_limit
                ]);

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
