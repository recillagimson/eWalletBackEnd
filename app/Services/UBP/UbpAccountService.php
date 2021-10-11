<?php

namespace App\Services\UBP;

use App\Models\UBP\UbpAccountToken;
use App\Repositories\UBPAccountToken\IUBPAccountTokenRepository;
use App\Traits\Errors\WithUbpErrors;
use Carbon\Carbon;

class UbpAccountService implements IUbpAccountService
{
    use WithUbpErrors;

    private IUBPAccountTokenRepository $ubpTokens;

    public function __construct(IUBPAccountTokenRepository $ubpTokens)
    {
        $this->ubpTokens = $ubpTokens;
    }

    public function checkAccountLink(string $userId): UbpAccountToken
    {
        $token = $this->ubpTokens->getByUser($userId);
        if (!$token) $this->ubpNoAccountLinked();

        $currentDate = Carbon::now();
        if ($currentDate->greaterThan($token->expires_in)) $this->ubpAccountLinkExpired();

        return $token;
    }
}
