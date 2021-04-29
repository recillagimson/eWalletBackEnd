<?php

namespace App\Services\Dashboard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

//Repository
use App\Repositories\UserAccount\IUserAccountRepository;

class DashboardService implements IDashboardService
{
    public IUserAccountRepository $userDetail;

    public function __construct(IUserAccountRepository $userdetail)
    {
        $this->userDetail = $userdetail;
    }

    public function dashboard(string $UserID)
    {
        //Get user details
        $UserDetails = $this->userDetail->getUserInfo($UserID);
        return $UserDetails;
    }
}
