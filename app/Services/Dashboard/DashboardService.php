<?php

namespace App\Services\Dashboard;

use App\Jobs\Transactions\ProcessUserPending;
use App\Repositories\Dashboard\IDashboard2022Repository;
use App\Repositories\Dashboard\ITransactionCountDailyRepository;
use App\Repositories\Dashboard\ITransactionCountMonthlyRepository;
use App\Repositories\Dashboard\ITransactionCountWeeklyRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Transaction\ITransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

//Repository

class DashboardService implements IDashboardService
{
    public IUserAccountRepository $userDetail;
    private ITransactionService $transactionService;
    private IUserAccountRepository $userAccounts;
    private IDashboard2022Repository $dashboard2022;
    private ITransactionCountDailyRepository $transactionCountDaily;
    private ITransactionCountMonthlyRepository $transactionCountMonthly;
    private ITransactionCountWeeklyRepository $transactionCountWeekly;

    public function __construct(IUserAccountRepository             $userdetail,
                                IUserAccountRepository             $userAccounts,
                                ITransactionService                $transactionService,
                                IDashboard2022Repository           $dashboard2022,
                                ITransactionCountDailyRepository   $transactionCountDaily,
                                ITransactionCountMonthlyRepository $transactionCountMonthly,
                                ITransactionCountWeeklyRepository  $transactionCountWeekly)
    {
        $this->userDetail = $userdetail;
        $this->transactionService = $transactionService;
        $this->userAccounts = $userAccounts;
        $this->dashboard2022 = $dashboard2022;
        $this->transactionCountDaily = $transactionCountDaily;
        $this->transactionCountMonthly = $transactionCountMonthly;
        $this->transactionCountWeekly = $transactionCountWeekly;
    }

    public function dashboard(string $userID)
    {
        //Get user details
        $user = $this->userAccounts->getUser($userID);
        $UserDetails = $this->userDetail->getUserInfo($userID);

        ProcessUserPending::dispatch($user);

        if ($UserDetails) {
            return $UserDetails;
        } else {
            throw ValidationException::withMessages([
                'user_details' => "Current user's details can't be found."
            ]);
        }
    }

    public function getDashboard2022(): Collection
    {
        return $this->dashboard2022->getAll();
    }

    public function getTransactionCountDaily(): Collection
    {
        return $this->transactionCountDaily->getAll();
    }

    public function getTransactionCountMonthly(): Collection
    {
        return $this->transactionCountMonthly->getAll();
    }

    public function getTransactionCountWeekly(): Collection
    {
        return $this->transactionCountWeekly->getAll();
    }
}
