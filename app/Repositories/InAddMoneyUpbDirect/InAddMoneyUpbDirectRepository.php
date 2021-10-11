<?php

namespace App\Repositories\InAddMoneyUpbDirect;

use App\Enums\UbpCredentials;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionCategories;
use App\Enums\SquidPayModuleTypes;
use App\Enums\UbpResponseCodes;
use App\Models\InAddMoneyUpbDirect;
use App\Models\UserBalanceInfo;
use App\Repositories\ReferenceCounter\IReferenceCounterRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class InAddMoneyUpbDirectRepository extends Repository implements IInAddMoneyUpbDirectRepository
{
    use WithUserErrors, WithTransactionErrors;

    private $userBalanceInfo;
    private IUserAccountRepository $userAccounts;
    private IReferenceCounterRepository $referenceCounterRepository;
    private ILogHistoryService $logHistoryService;

    public function __construct(
        InAddMoneyUpbDirect $model,
        IReferenceCounterRepository $referenceCounterRepository,
        IUserAccountRepository $userAccounts,
        UserBalanceInfo $userBalanceInfo,
        ILogHistoryService $logHistoryService
    )
    {
        $this->referenceCounterRepository = $referenceCounterRepository;
        $this->userAccounts = $userAccounts;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->logHistoryService = $logHistoryService;
        parent::__construct($model);
    }

    public function addMoney($data)
    {
        $user = $this->userAccounts->getUser($data['state']['id']);
        if (!$user) {
            $this->userAccountNotFound();
        }
        $response = Http::asForm()->accept('application/json')->post(UbpCredentials::UserTokenEndpoint, [
            'grant_type' => 'authorization_code',
            'client_id' => UbpCredentials::ClientId,
            'redirect_uri' => UbpCredentials::RedirectUri,
            'code' => $data['code'],
        ]);

        $upbCode = json_decode($response, true);

        if ($upbCode['error']) {
            $this->transactionNotFound();
        }
        $responseData = [
            "token" => $upbCode['access_token'],
            "state" => $data['state']
        ];
        $this->addMoneyForMerchant($responseData, $user);

    }

    public function addMoneyForMerchant($data, $user)
    {
        $response = Http::accept('application/json')->withHeaders([
            'x-ibm-client-id' => UbpCredentials::ClientId,
            'x-ibm-client-secret' => UbpCredentials::ClientSecret,
            'authorization' => 'Bearer ' . $data['token'],
            'x-partner-id' => UbpCredentials::PartnerId,
        ])->post(UbpCredentials::MerchantEndpoint, $this->merchantDetails($data['state'], $user));

        $ubpData = json_decode($response, true);
        if ($ubpData['errors']) {
            $this->transactionFailed();
        }
        $referenceNumber = $this->referenceCounterUpdate();
        $this->saveTransactionDetails($data, $user, $referenceNumber, $ubpData);

    }

    public function merchantDetails($data, $user)
    {
        $refId = "UBP".str_shuffle(time());
        return array (
            'senderRefId' => $refId,
            'tranRequestDate' => date_format(now(),"Y-m-d") . 'T' . date_format(now(),"H:i:s.v"),
            'amount' => array (
              'currency' => 'PHP',
              'value' => intval($data['amount']),
            ),
            'remarks' => $data['remarks'],
            'particulars' => 'Payment Particulars',
            'info' => array (
              0 => array (
                'index' => 1,
                'name' => 'Payor',
                'value' => $user->profile->full_name,
              ),
              1 => array (
                'index' => 2,
                'name' => 'InvoiceNo',
                'value' => $refId,
              ),
            ),
        );
    }

    public function saveTransactionDetails($data, $user, $referenceNumber, $upbData)
    {

        try {
            DB::beginTransaction();

            $upbArray = $this->inAddMoneyUpbDirectDetails($data, $referenceNumber, $upbData);
            $this->model->create($upbArray);

            //balance info update or create
            $userBalance = $this->userBalanceInfo->whereAccountUserId($data['state']['id'])->first();
            $userBalance->available_balance += intval($data['state']['amount']);
            $userBalance->save();

            if($upbData['payload']['code'] == UbpResponseCodes::successfulTransaction) {
                //log histories update or create
                $this->logHistoryService->logUserHistory($data['state']['id'],
                $referenceNumber,
                SquidPayModuleTypes::AddMoneyViaWebBanksUpbDirect,
                __NAMESPACE__,
                Carbon::now(),
                'Successfully Added Money On The Account via Upb Direct',
                TransactionCategories::AddMoneyWebBankUpbDirect);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Add Money Success Error: ', $e->getTrace());
        }

    }

    private function referenceCounterUpdate()
    {
        $reference = $this->referenceCounterRepository->getByCode('AB');
        $reference->counter = $reference->counter + 1;
        $reference->save();
        return $reference->counter;
    }

    private function inAddMoneyUpbDirectDetails($data, $referenceNumber, $ubpData)
    {
        return [
            "user_account_id" => $data['state']['id'],
            "reference_number" =>  $referenceNumber,
            "total_amount" =>  intval($data['state']['amount']),
            "transaction_date" =>  Carbon::now(),
            "transaction_category_id" =>  TransactionCategoryIds::cashinDragonPay,
            "transaction_remarks" =>  $data['state']['remarks'],
            "status" =>  $ubpData['payload']['code'],
            "ubp_reference" =>  $ubpData['payload']['ubpTranId'],
            "transaction_response" =>  $ubpData,
            "user_updated" =>  $data['state']['id'],
            "user_created" =>  $data['state']['id'],
        ];
    }
}
