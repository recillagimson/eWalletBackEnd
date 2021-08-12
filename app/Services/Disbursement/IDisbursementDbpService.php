<?php

namespace App\Services\Disbursement;

use App\Models\UserAccount;

/**
 * @property 
 * @property 
 *
 */
interface IDisbursementDbpService
{
    public function transaction(UserAccount $user, $fillRequest);
}
