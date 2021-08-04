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

    public function getAllPaginated($request, $perPage = 10);

    public function findById(string $id);

    public function updateEmail(string $email, object $user): array;

    public function validateEmail(string $userId, string $email);

    public function updateMobile(string $userId, string $mobile, UserAccount $user): array;

    public function validateMobile(string $userId, string $mobile);

    public function toggleActivation(string $userId): array;

    public function toggleLockout(string $userId): array;

}
