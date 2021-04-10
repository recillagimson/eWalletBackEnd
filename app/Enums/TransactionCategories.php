<?php

namespace App\Enums;

class TransactionCategories
{
    const SendMoneyToBankInstaPay = 'WITHDRAWUBPINSTAPAY';
    const SendMoneyToBankPesoNet = 'WITHDRAWUBPPESONET';
    const MerchantSendMoney = 'MXTRANSFER';
    const SendMoneyToSettlementAcct = 'CASHOUT';
    const AddMoneyWebBankDragonPay = 'CASHINDRAGONPAY';
    const DebitMemo = 'DR_MEMO';
    const CreditMemo = 'CR_MEMO';
    const Refund = 'POSREFUND';
    const ManualEntry = 'POSMANUAL';
    const POSPayment = 'POSPAYMERCHANT';
    const POSPayDriver = 'POSPAYDRIVER';
    const POSAddFunds = 'POSADDFUNDS';
    const POSFormat = 'POSFORMAT';
    const BuyLoad = 'CXLOAD';
    const CustomerSendMoney = 'CXSEND';
}
