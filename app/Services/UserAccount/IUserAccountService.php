<?php
namespace App\Services\UserAccount;

use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Collection;

interface IUserAccountService
{

    public function getAdminUsers(): Collection;

    public function getAdminUsersByEmail(string $email): Collection;

    public function getAdminUsersByName(string $lastName, string $firstName): Collection;

    public function createAdminUser(array $userInfo, string $userCreated): UserAccount;

    public function updateAdminUser(string $id, array $userInfo, string $userUpdated): UserAccount;

    public function deleteAdminUser(string $id);

    public function updateEmail(string $emailField, string $email, UserAccount $user);

    public function validateEmail(string $emailField, string $email);

    public function updateMobile(string $mobileField, string $mobile, UserAccount $user);

    public function validateMobile(string $mobileField, string $mobile);
}
