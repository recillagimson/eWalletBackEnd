<?php

namespace App\Services\Utilities\TierAndLimits;

use App\Enums\SquidPayModuleTypes;
use App\Models\UserAccount;
use App\Repositories\InAddMoney\IInAddMoneyRepository;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\Tier\ITierRepository;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class TierAndLimitsService implements ITierAndLimitsService
{
    /**
     * user account ID
     *
     * @var string
     */
    private $userAccountID;

    /**
     * The name of the current module
     *
     * @var string
     */
    private $module;

    private IInAddMoneyRepository $addMoneys;
    private IInReceiveMoneyRepository $receiveMoneys;
    private ITierRepository $tiers;
    private ILogHistoryRepository $logHistories;
    private IOutSend2BankRepository $sendToBanks;
    private IOutBuyLoadRepository $buyLoads;
    private IOutSendMoneyRepository $sendMoneys;

    public function __construct(IInAddMoneyRepository $addMoneys,
                                IInReceiveMoneyRepository $receiveMoneys,
                                ITierRepository $tiers,
                                ILogHistoryRepository $logHistories,
                                IOutSend2BankRepository $sendToBanks,
                                IOutBuyLoadRepository $buyLoads,
                                IOutSendMoneyRepository $sendMoneys) {
        
        $this->addMoneys = $addMoneys;
        $this->receiveMoneys = $receiveMoneys;
        $this->tiers = $tiers;
        $this->logHistories = $logHistories;
        $this->sendToBanks = $sendToBanks;
        $this->buyLoads = $buyLoads;
        $this->sendMoneys = $sendMoneys;
    }

    /**
     * Validates the current request amount based on the module and tier from user account
     *
     * @param float $amount
     * @param string $squiPayModule
     * @return void
     */
    public function validateTierAndLimits(float $amount, string $squiPayModule)
    {
        $user = request()->user();
        $this->setUserAccountID($user->id);
        $this->setModule($squiPayModule);

        $transAmountTotal = $this->getTotalTransAmountFromWhichModule($squiPayModule);

        $totalAmount = $amount + $transAmountTotal;

        $tier = $this->tiers->getTierByUserAccountId($this->userAccountID);

        if ($totalAmount > $tier->monthly_limit) return $this->tierLimitExceeded($totalAmount);
    }

    /**
     * Sets the userAccountID
     *
     * @param string $userAccountID
     * @return void
     */
    private function setUserAccountID(string $userAccountID)
    {
        $this->userAccountID = $userAccountID;
    }

    /**
     * Sets the module
     *
     * @param string $module
     * @return void
     */
    private function setModule(string $module)
    {
        $this->module = $module;
    }

    /**
     * Determine if the transaction is Credit in or Debit out. Then 
     * calculate the total of each model
     *
     * @param string $squiPayModule
     * @return float|exception
     */
    private function getTotalTransAmountFromWhichModule(string $squiPayModule)
    {
        $startOfThisMonth = Carbon::now()->startOfMonth();
        $endOfThisMonth = Carbon::now()->endOfMonth();

        switch ($squiPayModule) {
            case SquidPayModuleTypes::ReceiveMoney:
            case SquidPayModuleTypes::AddMoneyViaWebBanksDragonPay:
                    $addMoneyTotal = $this->addMoneys->getByUserAccountIDBetweenDates($this->userAccountID, $startOfThisMonth, $endOfThisMonth)->sum('amount');

                    $receiveMoneyTotal = $this->receiveMoneys->getByUserAccountIDBetweenDates($this->userAccountID, $startOfThisMonth, $endOfThisMonth)->sum('amount');

                    return $addMoneyTotal + $receiveMoneyTotal;
                break;

            case SquidPayModuleTypes::SendMoney:
            case SquidPayModuleTypes::SendToBankInstaPay:
            case SquidPayModuleTypes::SendToBankPesoNet:
            case SquidPayModuleTypes::SendToBankUnionBank:
                    $sendToBankTotal = $this->sendToBanks->getByUserAccountIDBetweenDates($this->userAccountID, $startOfThisMonth, $endOfThisMonth)->sum('amount');

                    $buyLoadTotal = $this->buyLoads->getByUserAccountIDBetweenDates($this->userAccountID, $startOfThisMonth, $endOfThisMonth)->sum('total_amount');

                    $sendMoneyTotal = $this->sendMoneys->getByUserAccountIDBetweenDates($this->userAccountID, $startOfThisMonth, $endOfThisMonth)->sum('amount');

                    return $sendMoneyTotal + $buyLoadTotal + $sendToBankTotal;
                break;
            
            default:
                return $this->moduleNonExistent();
                break;
        }
    }






    /**
     * Thrown when the request amount exceeded the limit
     * 
     * @param float $amount
     * @throws Exception
     */
    private function tierLimitExceeded($amount)
    {
        $this->logHistories->create([
            'user_account_id' => $this->userAccountID,
            'reference_number' => 'N/A',
            'squidpay_module' => $this->module,
            'namespace' => __METHOD__,
            'transaction_date' => Carbon::now(),
            'remarks' => 'Request exceeded tier limits.',
            'user_created' => $this->userAccountID,
            'user_updated' => $this->userAccountID
        ]);
        
        throw ValidationException::withMessages([
            'amount' => 'The requested amount (pending & current) exceeded the limits for this account.',
            'total_amount' => $amount
        ]);
    }

    /**
     * Thrown when the squidpay module indicated is not in the switch
     * statement
     *
     * @throws Error500
     */
    private function moduleNonExistent()
    {
        $this->logHistory->create([
            'user_account_id' => $this->userAccountID,
            'reference_number' => 'N/A',
            'squidpay_module' => $this->module,
            'namespace' => __METHOD__,
            'transaction_date' => Carbon::now(),
            'remarks' => 'Someone tried to enter an undeclared module.',
            'user_created' => $this->userAccountID,
            'user_updated' => $this->userAccountID
        ]);

        $this->throw500();
    }

    /**
     * Throws error 500. Abort
     *
     * @throws error500
     */
    private function throw500()
    {
        abort(500, 'Something went wrong :(');
    }

}