<?php

namespace App\Services\Dashboard;
use App\Jobs\Transactions\ProcessUserPending;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Transaction\ITransactionService;
use Illuminate\Validation\ValidationException;

//Repository

class DashboardService implements IDashboardService
{
    public IUserAccountRepository $userDetail;
    private ITransactionService $transactionService;
    private IUserAccountRepository $userAccounts;

    public function __construct(IUserAccountRepository $userdetail,
                                IUserAccountRepository $userAccounts,
                                ITransactionService    $transactionService)
    {
        $this->userDetail = $userdetail;
        $this->transactionService = $transactionService;
        $this->userAccounts = $userAccounts;
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
}
