<?php

namespace App\Services\AddMoney\DragonPay\PostBack;

use App\Exceptions\RecordUpdateException;
use App\Repositories\AddMoney\IWebBankRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HandlePostBackService implements IHandlePostBackService
{
    private IWebBankRepository $webBanks;
    private IUserBalanceInfoRepository $balanceInfos;

    public function __construct(IWebBankRepository $webBanks,
                                IUserBalanceInfoRepository $balanceInfos) {

        $this->webBanks = $webBanks;
        $this->balanceInfos = $balanceInfos;
    }

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

        // if Add Money row is missing, already updated to SUCCESS or FAILED
        $this->validate($referenceNumber);
        
        $addMoneyRow = $this->webBanks->getByReferenceNumber($referenceNumber);

        if ($status == 'S') {
            $status = 'SUCCESS';
            $responseData = (object) [
                'status' => true,
                'amount' => $addMoneyRow->amount,
                'message' => 'Added money successfully.'
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

    public function validate(string $referenceNumber)
    {
        $addMoneyRow = $this->webBanks->getByReferenceNumber($referenceNumber);

        if (!isset($addMoneyRow->status)) {
            throw new ModelNotFoundException();
        }

        if (isset($addMoneyRow->status) && $addMoneyRow->status == 'SUCCESS') {
            throw new RecordUpdateException();
        }

        if (isset($addMoneyRow->status) && $addMoneyRow->status == 'FAILED') {
            throw new RecordUpdateException();
        }
    }

    public function addAmountToUserBalance(string $userAccountID, float $amount)
    {
        $userBalanceInfo = $this->balanceInfos->getByUserAccountID($userAccountID);

        if ($userBalanceInfo == null) throw new ModelNotFoundException();
        
        return $this->balanceInfos->update($userBalanceInfo, ['available_balance' => $amount]);
    }
}
