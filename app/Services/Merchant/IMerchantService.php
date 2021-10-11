<?php

namespace App\Services\Merchant;

/**
 * @property 
 * @property 
 *
 */
interface IMerchantService
{
    public function list(array $attr);
    public function toggleMerchantStatus(array $attr);
    public function verifyMerchant(array $attr);
    public function showDocument(string $merchantId);
    public function updateDocumentStatus(array $attr);
}
