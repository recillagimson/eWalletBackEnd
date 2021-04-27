<?php


namespace App\Services\Send2Bank;


use App\Enums\ReferenceNumberTypes;
use App\Enums\TpaProviders;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Errors\WithUserErrors;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class Send2BankPesonetService implements ISend2BankService
{
    use WithAuthErrors, WithUserErrors, WithTpaErrors, WithTransactionErrors;

    private IReferenceNumberService $referenceNumberService;

    private IUserAccountRepository $users;

    public function __construct(IUBPService $ubpService, IReferenceNumberService $referenceNumberService,
                                IUserAccountRepository $users)
    {
        $this->ubpService = $ubpService;
        $this->referenceNumberService = $referenceNumberService;

        $this->users = $users;
    }


    public function getBanks(): array
    {
        $response = $this->ubpService->getBanks(TpaProviders::ubpPesonet);
        if (!$response->successful()) $this->tpaErrorOccured('UBP - Pesonet');
        return json_decode($response->body())->records;
    }


    public function fundTransfer(string $fromUserId, array $recipient)
    {
        try {
            DB::beginTransaction();

            $user = $this->users->getUser($fromUserId);
            if (!$user) $this->accountDoesntExist();
            if (!$user->profile) $this->userProfileNotUpdated();
            if (!$user->balanceInfo) $this->userInsufficientBalance();

            $userFullName = "SquidPay";
            $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);
            $transactionDate = Carbon::now()->toDateTimeLocalString('millisecond');

            $transferResponse = $this->ubpService->fundTransfer($refNo, $userFullName, $recipient['bank_code'],
                $recipient['account_number'], $recipient['account_name'], $recipient['amount'], $transactionDate,
                $recipient['message'], TpaProviders::ubpPesonet);

            if (!$transferResponse->successful()) {
                $errors = $transferResponse->json();
                $this->transFailed();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


}
