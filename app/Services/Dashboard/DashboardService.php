<?php

namespace App\Services\Dashboard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

//Repository
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;

class DashboardService implements IDashboardService
{
    public IUserDetailRepository $userDetail;

    public function __construct(IUserDetailRepository $userdetail)
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
