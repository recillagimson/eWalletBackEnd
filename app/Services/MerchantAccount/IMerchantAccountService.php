<?php

namespace App\Services\MerchantAccount;

/**
 * @property 
 * @property 
 *
 */
interface IMerchantAccountService
{
    public function create(array $attr);
    public function setUserMerchantAccount(array $attr);
    public function createMerchantAccount(array $attr);
}
