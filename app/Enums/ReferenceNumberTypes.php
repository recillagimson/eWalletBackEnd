<?php

namespace App\Enums;

class ReferenceNumberTypes
{
    const SendMoney = 'SM';
    const SendToBank = 'SB';
    const PayBills = 'PB';
    const Remittance = 'RM';
    const BuyLoad = 'BL';
    const AddMoneyViaWebBank = 'AB';
    const AddMoneyViaOTC = 'AC'; //Over the counter
    const AddMoneyViaSquidPay = 'AS';
    const DR = 'DR';
    const CR = 'CR';
}
