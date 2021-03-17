<?php
namespace App\Services\Auth;


use App\Repositories\UserAccount\IUserAccountRepository;

/**
 * @property IUserAccountRepository $userAccounts
 *
 */
interface IAuthService {
    public function register(array $newUser);
    public function login(string $usernameField, array $creds, string $ip);
}
