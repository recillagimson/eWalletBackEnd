<?php

namespace App\Services\AddMoney;

use App\Models\UserAccount;
use App\Services\AddMoney\Providers\IAddMoneyService;

class InAddMoneyService implements IInAddMoneyService
{
    private IAddMoneyService $addMoneyService;

    public function __construct(IAddMoneyService $addMoneyService) {
        $this->addMoneyService = $addMoneyService;
    }

    public function addMoney(UserAccount $user, array $request)
    {
        return $this->addMoneyService->addMoney($user, $request);
    }
    
    public function cancelAddMoney(UserAccount $user, array $referenceNumber)
    {
        return $this->addMoneyService->cancelAddMoney($user, $referenceNumber);
    }
}
