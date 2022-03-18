<?php


namespace App\Services\Auth;


use App\Models\UserAccount;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Traits\Errors\WithErrors;
use App\Traits\Errors\WithUserErrors;

class UserService implements IUserService
{
    use WithUserErrors;
    private IUserAccountRepository $users;

    public function __construct(IUserAccountRepository $users)
    {
        $this->users = $users;
    }

    public function getBalanceInfo(string $userId): array
    {
        $user = $this->users->getUser($userId);
        if(!$user) $this->userAccountNotFound();

        return $user->balanceInfo->toArray();
    }
}
