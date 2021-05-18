<?php
namespace App\Services\UserAccount;

interface IUserAccountService {

    public function updateEmail(string $usernameField, string $username, object $user);
    public function validateEmail(string $usernameField, string $username);
    public function isAUserAccount($userAccount);
    public function updateMobile(string $mobileField, string $mobile, object $user);
    public function validateMobile(string $mobileField, string $mobile);
}
