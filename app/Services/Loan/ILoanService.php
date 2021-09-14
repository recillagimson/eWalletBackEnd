<?php

namespace App\Services\Loan;

/**
 * @property 
 * @property 
 *
 */
interface ILoanService
{
    public function generateReferenceNumber();
    public function storeReferenceNumber(array $attr);
}
