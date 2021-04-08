<?php

namespace App\Services\AddMoney\DragonPay\PostBack;

use App\Repositories\AddMoney\IWebBankRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use Illuminate\Validation\ValidationException;

class HandlePostBackService implements IHandlePostBackService
{
    private IWebBankRepository $webBanks;
    private IUserBalanceInfoRepository $balanceInfos;

    public function __construct(IWebBankRepository $webBanks,
                                IUserBalanceInfoRepository $balanceInfos) {

        $this->webBanks = $webBanks;
        $this->balanceInfos = $balanceInfos;
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

        $message = explode(' ', $message);
        $channelNum = $message[0];
        $channelCode = $message[1];
        $channelRefNo = $message[4];

        // if Add Money row is missing or the record does not have a 'PENDING' status
        $this->validate($referenceNumber);
        
        $addMoneyRow = $this->webBanks->getByReferenceNumber($referenceNumber);

        if ($status == 'S') {
            $status = 'SUCCESS';
            $responseData = (object) [
                'status' => true,
                'amount' => $addMoneyRow->amount,
                'message' => 'Money added successfully.'
            ];

            $this->addAmountToUserBalance($addMoneyRow->user_account_id, $addMoneyRow->amount);
        }

        if ($status == 'F') {
            $status = 'FAILED';
            $responseData = (object) [
                'status' => false,
                'amount' => $addMoneyRow->amount,
                'message' => 'Failed to add money.'
            ];
        }

        $this->webBanks->update($addMoneyRow, [
            'dragonpay_reference' => $dragonpayReference,
            'dragonpay_channel_reference_number' => $channelRefNo,
            'status' => $status
        ]);

        return $responseData;
    }

    /**
     * Validates the transaction's status
     * 
     * @param string $referenceNumber
     * @return exception
     */
    public function validate(string $referenceNumber)
    {
        $addMoneyRow = $this->webBanks->getByReferenceNumber($referenceNumber);

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
        
        return $this->balanceInfos->update($userBalanceInfo, ['available_balance' => $amount]);
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
