<?php


namespace App\Services\v2\Auth;


use App\Models\UserAccount;

interface IUserService
{
    public function updatePassword(UserAccount $user, $password);
}
