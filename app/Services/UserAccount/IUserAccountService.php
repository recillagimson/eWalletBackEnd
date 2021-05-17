<?php
namespace App\Services\UserAccount;

interface IUserAccountService {

    public function updateEmail(string $emailField, string $email, object $user);
    public function validateEmail(string $emailField, string $email);
    public function updateMobile(string $mobileField, string $mobile, object $user);
    public function validateMobile(string $mobileField, string $mobile);
}
