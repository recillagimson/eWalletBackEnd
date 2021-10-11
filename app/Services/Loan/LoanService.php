<?php

namespace App\Services\Loan;

use App\Enums\ReferenceNumberTypes;
use App\Repositories\Loan\ILoanRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\UserAccount\IUserAccountService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithUserErrors;

class LoanService implements ILoanService
{
    private IReferenceNumberService $referenceNumberService;
    private ILoanRepository $loanRepository;
    private IEmailService $emailService;
    private ISmsService $smsService;
    private IUserAccountRepository $userAccount;
    private IUserDetailRepository $userDetail;

    use WithUserErrors;

    public function __construct(IReferenceNumberService $referenceNumberService, 
    ILoanRepository $loanRepository, IEmailService $emailService, ISmsService $smsService,
    IUserAccountRepository $userAccount, IUserDetailRepository $userDetail
    )
    {
        $this->referenceNumberService = $referenceNumberService;
        $this->loanRepository = $loanRepository;
        $this->smsService = $smsService;
        $this->emailService = $emailService;
        $this->userAccount = $userAccount;
        $this->userDetail = $userDetail;
    }


    public function generateReferenceNumber() {
        $ref = $this->referenceNumberService->generate(ReferenceNumberTypes::Loan);
        return $ref;
    }

    public function storeReferenceNumber(array $attr) {
        try {

            $userAccount = $this->userAccount->get($attr['user_account_id']);
            $userDetail = $this->userDetail->getByUserId($attr['user_account_id']);

            if(!$userAccount && $userDetail) {
                $this->userAccountNotFound();
            }

            if($userAccount && $userDetail && $userDetail->email) {
                $this->emailService->sendLoanReferenceNumber($userDetail->first_name, $attr['reference_number'], $userAccount->email);
            }

            if($userAccount && $userDetail && $userDetail->mobile_number) {
                $this->smsService->sendLoanConfirmation($userAccount->mobile_number, $userDetail->first_name, $attr['reference_number']);
            }

            $record = $this->loanRepository->create([
                'user_account_id' => $attr['user_account_id'],
                'reference_number' => $attr['reference_number'],
                'status' => 'PENDING',
                'user_created' => request()->user()->id,
                'user_updated' => request()->user()->id,
            ]);
    
            return $record;
        } catch(\Exception $err) {
            throw $err;
            return;
        }
    }
}