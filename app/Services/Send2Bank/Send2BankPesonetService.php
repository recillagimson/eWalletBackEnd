<?php


namespace App\Services\Send2Bank;


use App\Enums\ReferenceNumberTypes;
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
use Throwable;

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

    /**
     * Returns a listing of PesoNet supported banks
     *
     * @return array
     */
    public function getBanks(): array
    {
        $response = $this->ubpService->getPesonetBanks();
        if (!$response->successful()) $this->tpaErrorOccured('UBP - Pesonet');
        return json_decode($response->body())->records;
    }

    /**
     * Fund transfer to recepient bank acccount
     *
     * @param string $fromUserId
     * @param array $recipient
     * @throws Throwable
     */
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

            $transferResponse = $this->ubpService->pesonetFundTransfer($refNo, $userFullName, $recipient['bank_code'],
                $recipient['account_number'], $recipient['account_name'], $recipient['amount'], $transactionDate,
                $recipient['message']);

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
