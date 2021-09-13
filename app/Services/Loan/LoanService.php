<?php

namespace App\Services\Loan;

use App\Enums\ReferenceNumberTypes;
use App\Repositories\Loan\ILoanRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;

class LoanService implements ILoanService
{
    private IReferenceNumberService $referenceNumberService;
    private ILoanRepository $loanRepository;

    public function __construct(IReferenceNumberService $referenceNumberService, 
    ILoanRepository $loanRepository
    )
    {
        $this->referenceNumberService = $referenceNumberService;
        $this->loanRepository = $loanRepository;
    }


    public function generateReferenceNumber() {
        $ref = $this->referenceNumberService->generate(ReferenceNumberTypes::Loan);
        return $ref;
    }

    public function storeReferenceNumber(array $attr) {
        $record = $this->loanRepository->create([
            'user_account_id' => $attr['user_account_id'],
            'reference_number' => $attr['reference_number'],
            'status' => 'PENDING',
            'user_created' => request()->user()->id,
            'user_updated' => request()->user()->id,
        ]);

        return $record;
    }
}