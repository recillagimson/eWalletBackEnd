<?php

namespace App\Enums;

class SquidPayModuleTypes
{
    const SendMoney = 'SEND_MONEY';
    const PayBills = 'PAY_BILLS';
    const Remittance = 'REMITTANCE';
    const BuyLoad = 'BUY_LOAD';
    const AddMoneyViaWebBanksDragonPay = 'ADD_MONEY_WEB_BANK_DRAGONPAY';
    const AddMoneyViaOTCDragonPay = 'ADD_MONEY_OTC_DRAGONPAY'; //Over the counter
    const AddMoneyViaSquidPay = 'ADD_MONEY_SQUIDPAY';
}
