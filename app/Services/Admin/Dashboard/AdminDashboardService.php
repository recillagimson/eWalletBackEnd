<?php

namespace App\Services\Admin\Dashboard;

use App\Repositories\Dashboard\IDashboardRepository;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Repositories\InAddMoney\IInAddMoneyRepository;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use Illuminate\Validation\ValidationException;

//Repository

class AdminDashboardService implements IAdminDashboardService
{
    // public IUserAccountRepository $userDetail;
    // public IInAddMoneyRepository $addMoney;
    // public IInReceiveMoneyRepository $receiveMoney;
    // public IOutBuyLoadRepository $outBuyLoad;
    // public IOutPayBillsRepository $outPayBills;
    // public IOutSendMoneyRepository $outSendMoney;
    // public IOutSend2BankRepository $outSend2Bank;
    // public IDrcrMemoRepository $drMemo;

    private IDashboardRepository $dashboardRepo;

    public function __construct(IDashboardRepository $dashboardRepo)

    {
        $this->dashboardRepo = $dashboardRepo;
        // $this->userDetail = $userdetail;
        // $this->addMoney = $addMoney;
        // $this->receiveMoney = $receiveMoney;
        // $this->outBuyLoad = $outBuyLoad;
        // $this->outPayBills = $outPayBills;
        // $this->outSendMoney = $outSendMoney;
        // $this->outSend2Bank = $outSend2Bank;
        // $this->drMemo = $drMemo;
    }

    public function dashboard()
    {
        // //Get user count
        // $UserDetails = $this->userDetail->getUserCount();
        // //Get Total cashin
        // $totalCashin = $this->addMoney->getTotalAddMoney();
        // //Get Total received money
        // $totalReceiveMoney = $this->receiveMoney->getTotalReceiveMoney();
        // //TOTAL SUM (add money + received money)
        // $TotalAmount = $totalCashin + $totalReceiveMoney;
        // //Total BuyLoad
        // $TotalBuyLoad = $this->outBuyLoad->totalBuyload();
        // //Total paybills
        // $TotalPayBills = $this->outPayBills->totalPayBills();
        // //amount (paybills)
        // $PayBillsAmount = $this->outPayBills->totalamountPayBills();
        // //other charges (paybills)
        // $PayBillsOtherCharges = $this->outPayBills->totalotherchargesPayBills();
        // //service fee (paybills)
        // $PayBillsServiceFee = $this->outPayBills->totalservicefeePayBills();
        // //Total Send Money
        // $TotalSendMoney = $this->outSendMoney->totalSendMoney();
        // //amount (send money)
        // $SendMoneyAmount = $this->outSendMoney->totalamountSendMoney();
        // //service fee (send money)
        // $SendMoneyServiceFee = $this->outSendMoney->totalservicefeeSendMoney();
        // //Total Send to Bank
        // $TotalSend2Bank = $this->outSend2Bank->totalSend2Bank();
        // //Total Debit Memo
        // $TotalDRMemo = $this->drMemo->totalDRMemo();
        // //Total CR Memo
        // $TotalCRMemo = $this->drMemo->totalCRMemo();

        // //Total Disbursement
        // $TotalDisbursement = $TotalBuyLoad + $TotalPayBills + $TotalSendMoney + $TotalSend2Bank + $TotalDRMemo;
        // //Total Collection
        // $TotalCollection = $totalCashin + $TotalCRMemo;
        // //Total Available Balance
        // $TotalAvailableBalance = $TotalCollection - $TotalDisbursement;


        // if($UserDetails)
        // {
        //     $arr = [
        //         'paybills_amount'   =>  $PayBillsAmount,
        //         'paybills_other_charges'    =>  $PayBillsOtherCharges,
        //         'paybills_service_fee'  =>  $PayBillsServiceFee,
        //         'customer_count'    =>  $UserDetails,
        //         'total_transaction' =>  '0',
        //         'total_cashin'      =>  $TotalAmount,
        //         'sendmoney_amount'  =>  $SendMoneyAmount,
        //         'sendmoney_service_fee' =>  $SendMoneyServiceFee,
        //         'total_disbursement'    =>  $TotalDisbursement,
        //         'total_collection'  =>  $TotalCollection,
        //         'total_available_funds' => $TotalAvailableBalance,
        //     ];

        //     return $arr;
        // }
        // else
        // {
        //     throw ValidationException::withMessages([
        //         'user_details' => "Current user's details can't be found."
        //     ]);
        // }

        return $this->dashboardRepo->getDashboardData();

    }
}