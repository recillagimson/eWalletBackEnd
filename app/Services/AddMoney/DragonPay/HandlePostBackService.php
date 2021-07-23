<?php

namespace App\Services\AddMoney\DragonPay;

use App\Enums\DragonPayStatusTypes;
use App\Enums\SquidPayModuleTypes;
use App\Enums\SuccessMessages;
use App\Enums\TransactionCategories;
use App\Repositories\InAddMoney\IInAddMoneyRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Log;

class HandlePostBackService implements IHandlePostBackService
{
    /**
     * Transaction name of this class
     *
     * @var string
     */
    protected string $moduleTransCategory;

    /**
     * Current transaction's reference number, the one that
     * SquidPay generated
     *
     * @var string
     */
    protected string $referenceNumber;

    /**
     * Authenticated user's user account ID
     *
     * @var string
     */
    protected string $userAccountID;

    private string $secretKey;

    private IInAddMoneyRepository $addMoneys;
    private IUserBalanceInfoRepository $balanceInfos;
    private IUserTransactionHistoryRepository $userTransactions;
    private ILogHistoryRepository $logHistories;
    private IResponseService $responseService;
    private ILogHistoryService $logHistoryService;
    private IUserTransactionHistoryRepository $transactionHistories;

    public function __construct(IInAddMoneyRepository $addMoneys,
                                IUserBalanceInfoRepository $balanceInfos,
                                IUserTransactionHistoryRepository $transactionHistories,
                                IUserTransactionHistoryRepository $userTransactions,
                                ILogHistoryRepository $logHistories,
                                IResponseService $responseService,
                                ILogHistoryService $logHistoryService) {

        $this->moduleTransCategory = TransactionCategories::AddMoneyWebBankDragonPay;

        $this->addMoneys = $addMoneys;
        $this->balanceInfos = $balanceInfos;
        $this->userTransactions = $userTransactions;
        $this->logHistories = $logHistories;
        $this->responseService = $responseService;
        $this->logHistoryService = $logHistoryService;
        $this->transactionHistories = $transactionHistories;

        $this->secretKey = config('dragonpay.dp_key');
    }

    /**
     * The core method in this service.
     * Inserts the data receive from DragonPay
     * postback
     *
     * @param array $postBackData
     * @return object $responseData
     * @throws ValidationException
     */
    public function insertPostBackData(array $postBackData): object
    {
        $referenceNumber = $postBackData['txnid'];
        $dragonpayReference = $postBackData['refno'];
        $status = $postBackData['status'];
        $message = $postBackData['message'];

        //$this->validatePayLoad($postBackData);
        $this->validate($referenceNumber);

        $addMoneyRow = $this->addMoneys->getByReferenceNumber($referenceNumber);
        $amountForResponse = ['amount' => $addMoneyRow->amount];

        $this->setReferenceNumber($referenceNumber);
        $this->setUserAccountID($addMoneyRow->user_account_id);

        if ($status == 'S') {
            $message = explode(' ', $message);
            $channelRefNo = $message[4];

            $this->addMoneys->update($addMoneyRow, [
                'dragonpay_reference' => $dragonpayReference,
                'dragonpay_channel_reference_number' => $channelRefNo,
                'status' => DragonPayStatusTypes::Success
            ]);

            $this->addAmountToUserBalance($addMoneyRow->user_account_id, $addMoneyRow->amount);

            $this->logHistoryService->logUserHistoryUnauthenticated($this->userAccountID, $this->referenceNumber, SquidPayModuleTypes::AddMoneyViaWebBanksDragonPay, __METHOD__, Carbon::now(), 'Successfully added money from DragonPay with amount of ' . $addMoneyRow->amount);
            $this->transactionHistories->log(
                $addMoneyRow->user_account_id,
                $addMoneyRow->transaction_category_id,
                $addMoneyRow->id,
                $addMoneyRow->reference_number,
                $addMoneyRow->amount,
                $addMoneyRow->transaction_date,
                $addMoneyRow->user_account_id
            );


            return $this->responseService->successResponse(
                $amountForResponse,
                SuccessMessages::addMoneySuccess
            );
        }

        if ($status == 'F') {

            $this->addMoneys->update($addMoneyRow, [
                'dragonpay_reference' => $dragonpayReference,
                'dragonpay_channel_reference_number' => isset($channelRefNo) ? $channelRefNo : 'N/A',
                'transaction_remarks' => $message,
                'status' => DragonPayStatusTypes::Failure
            ]);

            $this->logHistoryService->logUserHistoryUnauthenticated($this->userAccountID, $this->referenceNumber, SquidPayModuleTypes::AddMoneyViaWebBanksDragonPay, __METHOD__, Carbon::now(), SuccessMessages::addMoneyFailed);

            throw ValidationException::withMessages(['Message' => 'Add money Failed']);
        }

        if ($status == 'P') {

            $this->addMoneys->update($addMoneyRow, [
                'dragonpay_reference' => $dragonpayReference,
                'dragonpay_channel_reference_number' => isset($channelRefNo) ? $channelRefNo : 'N/A',
                'transaction_remarks' => $message,
                'status' => DragonPayStatusTypes::Pending
            ]);

            $this->logHistoryService->logUserHistoryUnauthenticated($this->userAccountID, $this->referenceNumber, SquidPayModuleTypes::AddMoneyViaWebBanksDragonPay, __METHOD__, Carbon::now(), SuccessMessages::addMoneyPending);

            return $this->responseService->successResponse(['dragonpay_reference' => $referenceNumber, 'amount' => $addMoneyRow->amount], SuccessMessages::addMoneyPending);
        }
    }

    /**
     * Set the reference number
     *
     * @param string $referenceNumber
     * @return void
     */
    public function setReferenceNumber(string $referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * Set the user account ID
     *
     * @param string $userAccountID
     * @return void
     */
    public function setUserAccountID(string $userAccountID)
    {
        $this->userAccountID = $userAccountID;
    }

    /**
     * Validates the transaction's status
     *
     * @param string $referenceNumber
     * @throws ValidationException
     */
    public function validate(string $referenceNumber)
    {
        $addMoneyRow = $this->addMoneys->getByReferenceNumber($referenceNumber);

        if (!isset($addMoneyRow->status)) {

            $this->transactionNotFound();
        }

        if (isset($addMoneyRow->status) && $addMoneyRow->status != 'PENDING') {
            $this->transactionIsUpToDate();
        }
    }

    /**
     * Credit the cashed in amount to the user
     *
     * @param string $userAccountID
     * @param float $amount
     * @return bool
     * @throws ValidationException
     */
    public function addAmountToUserBalance(string $userAccountID, float $amount): bool
    {
        $userBalanceInfo = $this->balanceInfos->getByUserAccountID($userAccountID);

        if ($userBalanceInfo == null) $this->userBalanceInfoNotFound();

        $balance = $userBalanceInfo->available_balance + $amount;

        return $this->balanceInfos->update($userBalanceInfo, ['available_balance' => $balance]);
    }

    /**
     * Logs the user's transaction in user transaction table
     *
     * @param string $remarks
     * @return void
     */
    public function logTransaction(string $remarks)
    {
        $this->logHistories->create([
            'user_account_id' => $this->userAccountID,
            'reference_number' => $this->referenceNumber,
            'namespace' => __METHOD__,
            'transaction_date' => Carbon::now(),
            'remarks' => $remarks,
            'user_created' => $this->userAccountID,
            'user_updated' => $this->userAccountID
        ]);
    }

    /**
     * Logs the user's transaction (generate URL) in
     * user transaction history
     *
     * @param string $transactionID
     * @param string $transCategoryID
     * @return void
     */
    public function logSuccessInUserTransHistory(string $transactionID, string $transCategoryID)
    {
        $this->userTransactions->create([
            'user_account_id' => $this->userAccountID,
            'transaction_id' => $transactionID,
            'reference_number' => $this->referenceNumber,
            'transaction_category_id' => $transCategoryID,
            'user_created' => $this->userAccountID,
            'user_updated' => $this->userAccountID
        ]);
    }


    /**
     * Thrown when the Transaction record row
     * is not found
     */
    private function transactionNotFound()
    {
        throw ValidationException::withMessages([
            'refno' => 'Transaction record not found.'
        ]);
    }

    /**
     * Thrown when the transaction record is already updated
     */
    private function transactionIsUpToDate()
    {
        throw ValidationException::withMessages([
            'refno' => 'Transaction record is already up to date.'
        ]);
    }

    /**
     * Thrown when the User Balance info row is non existent
     */
    private function userBalanceInfoNotFound()
    {
        throw ValidationException::withMessages([
            'amount' => 'User balance info not found.'
        ]);
    }

    private function invalidPayload()
    {
        throw ValidationException::withMessages([
            'payload' => 'Payload is invalid.'
        ]);
    }

    private function validatePayLoad(array $data)
    {
        $strDigest = "{$data['txnid']}:{$data['refno']}:{$data['status']}:{$data['message']}:{$this->secretKey}}";
        $digest = sha1($strDigest);

        Log::debug('Digest Info:', [
            'plainText' => $strDigest,
            'digestFromPayload' => $digest,
            'originalDigest' => $data['digest']
        ]);

        if ($data['digest'] !== $digest) {
            $this->invalidPayload();
        }
    }
}
