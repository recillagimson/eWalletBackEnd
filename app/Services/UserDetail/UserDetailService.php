<?php

namespace App\Services\UserDetail;

use App\Repositories\UserDetail\IUserDetailRepository;

class UserDetailService implements IUserDetailService
{

    public IUserDetailRepository $userDetailsRepository;

    public function __construct(IUserDetailRepository $userDetailsRepository)
    {
        $this->userDetailsRepository = $userDetailsRepository;

    }

    public function addOrUpdate(object $userAccount) {
       
    }

}
