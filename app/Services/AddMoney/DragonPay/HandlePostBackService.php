<?php

namespace App\Services\AddMoney\DragonPay;

use App\Enums\DragonPayStatusTypes;
use App\Enums\SquidPayModuleTypes;
use App\Enums\SuccessMessages;
use App\Enums\TransactionCategories;
use App\Enums\TransactionStatuses;
use App\Repositories\InAddMoney\IInAddMoneyRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Transaction\ITransactionService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class HandlePostBackService implements IHandlePostBackService
{
    /**
     * Transaction name of this class
     * 
     * @var string
     */
    protected $moduleTransCategory;

    /**
     * Current transaction's reference number, the one that
     * SquidPay generated
     * 
     * @var string
     */
    protected $referenceNumber;

    /**
     * Authenticated user's user account ID
     * 
     * @var uuid
     */
    protected $userAccountID;

    private IInAddMoneyRepository $addMoneys;
    private IUserBalanceInfoRepository $balanceInfos;
    private IUserTransactionHistoryRepository $userTransactions;
    private ILogHistoryRepository $logHistories;
    private ITransactionCategoryRepository $transactionCategories;
    private IResponseService $responseService;
    private ITransactionService $transactionService;
    private ILogHistoryService $logHistoryService;

    public function __construct(IInAddMoneyRepository $addMoneys,
                                IUserBalanceInfoRepository $balanceInfos,
                                IUserTransactionHistoryRepository $userTransactions,
                                ILogHistoryRepository $logHistories,
                                ITransactionCategoryRepository $transactionCategories,
                                IResponseService $responseService,
                                ITransactionService $transactionService,
                                ILogHistoryService $logHistoryService) {

        $this->moduleTransCategory = TransactionCategories::AddMoneyWebBankDragonPay;

        $this->addMoneys = $addMoneys;
        $this->balanceInfos = $balanceInfos;
        $this->userTransactions = $userTransactions;
        $this->logHistories = $logHistories;
        $this->transactionCategories = $transactionCategories;
        $this->responseService = $responseService;
        $this->transactionService = $transactionService;
        $this->logHistoryService = $logHistoryService;
    }

    /**
     * The core method in this service.
     * Inserts the data receive from DragonPay
     * postback
     * 
     * @param array $postBackData
     * @return object $responseData
     */
    public function insertPostBackData(array $postBackData)
    {
        $referenceNumber = $postBackData['txnid'];
        $dragonpayReference = $postBackData['refno'];
        $status = $postBackData['status'];
        $message = $postBackData['message'];
        $digest = $postBackData['digest'];

        $transactionCategory = $this->transactionCategories->getByName($this->moduleTransCategory);
        
        // if Add Money row is missing or the record does not have a 'PENDING' status
        $this->validate($referenceNumber);

        $addMoneyRow = $this->addMoneys->getByReferenceNumber($referenceNumber);
        $amountForResponse = ['amount' => $addMoneyRow->amount];

        $this->setReferenceNumber($referenceNumber);
        $this->setUserAccountID($addMoneyRow->user_account_id);

        if ($status == 'S') {
            $message = explode(' ', $message);
            $channelNum = $message[0];
            $channelCode = $message[1];
            $channelRefNo = $message[4];

            $this->addMoneys->update($addMoneyRow, [
                'dragonpay_reference' => $dragonpayReference,
                'dragonpay_channel_reference_number' => $channelRefNo,
                'status' => TransactionStatuses::success
            ]);

            $this->addAmountToUserBalance($addMoneyRow->user_account_id, $addMoneyRow->amount);

            $this->logHistoryService->logUserHistoryUnauthenticated($this->userAccountID, $this->referenceNumber, SquidPayModuleTypes::AddMoneyViaWebBanksDragonPay, __METHOD__, Carbon::now(), 'Add money postback amount ' . $addMoneyRow->amount);
            $this->transactionService->createUserTransactionEntryUnauthenticated((string) $this->userAccountID, $addMoneyRow->id, $this->referenceNumber, (float) $addMoneyRow->amount,  $addMoneyRow->transaction_category_id);

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
                'status' => TransactionStatuses::failed
            ]);

            $this->logHistoryService->logUserHistoryUnauthenticated($this->userAccountID, $this->referenceNumber, SquidPayModuleTypes::AddMoneyViaWebBanksDragonPay, __METHOD__, Carbon::now(), SuccessMessages::addMoneyFailed);

            throw ValidationException::withMessages(['Message' => 'Add money Failed']);
        }

        if ($status == 'P') {

            $this->addMoneys->update($addMoneyRow, [
                'dragonpay_reference' => $dragonpayReference,
                'dragonpay_channel_reference_number' => isset($channelRefNo) ? $channelRefNo : 'N/A',
                'transaction_remarks' => $message,
                'status' => TransactionStatuses::pending
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
     * @return exception
     */
    public function validate(string $referenceNumber)
    {
        $addMoneyRow = $this->addMoneys->getByReferenceNumber($referenceNumber);

        if (!isset($addMoneyRow->status)) {

            return $this->transactionNotFound();
        }

        if (isset($addMoneyRow->status) && $addMoneyRow->status != 'PENDING') {

            return $this->transactionIsUpToDate();
        }
    }

    /**
     * Credit the cashed in amount to the user
     * 
     * @param string $userAccountID
     * @param float $amount
     * @return bool
     */
    public function addAmountToUserBalance(string $userAccountID, float $amount)
    {
        $userBalanceInfo = $this->balanceInfos->getByUserAccountID($userAccountID);

        if ($userBalanceInfo == null) return $this->userBalanceInfoNotFound();

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
     * @param uuid $transactionID
     * @param uuid $transCategoryID
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
}
