<?php


namespace App\Services\Auth;


use App\Models\UserAccount;

interface IUserService
{
    public function updatePassword(UserAccount $user, $password);
}
