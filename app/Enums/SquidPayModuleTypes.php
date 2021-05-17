<?php

namespace App\Enums;

class SquidPayModuleTypes
{
    const SendMoney = 'SEND_MONEY';
    const ReceiveMoney = 'RECEIVE_MONEY';
    const PayBills = 'PAY_BILLS';
    const Remittance = 'REMITTANCE';
    const BuyLoad = 'BUY_LOAD';
    const AddMoneyViaWebBanksDragonPay = 'ADD_MONEY_FROM_BANK_DRAGONPAY';
    const AddMoneyViaOTCDragonPay = 'ADD_MONEY_OTC_DRAGONPAY'; //Over the counter
    const AddMoneyViaSquidPay = 'ADD_MONEY_SQUIDPAY';
    const SendToBankInstaPay = 'SEND_TO_BANK_INSTAPAY';
    const SendToBankPesoNet = 'SEND_TO_BANK_PESONET';
    const SendToBankUnionBank = 'SEND_TO_BANK_UNION_BANK';
}
