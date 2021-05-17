<?php
namespace App\Services\UserAccount;

interface IUserAccountService {

    public function updateEmail(string $usernameField, string $username, object $user);
    public function validateEmail(string $usernameField, string $username);
    public function isAUserAccount($userAccount);
}
