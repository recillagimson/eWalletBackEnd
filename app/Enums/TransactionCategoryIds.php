<?php


namespace App\Enums;


class TransactionCategoryIds
{
    const send2BankInstaPay = '0ec41025-9131-11eb-b44f-1c1b0d14e211';
    const send2BankPesoNet = '0ec432e7-9131-11eb-b44f-1c1b0d14e211';
    const send2BankUBP = '0ec432e7-9131-11eb-b44f-1c1b0d14e211';

    const posAddFunds = '0ec436e0-9131-11eb-b44f-1c1b0d14e211';
    const cashinDragonPay = '0ec43457-9131-11eb-b44f-1c1b0d14e211';

    const sendMoneyToSquidPayAccount = '1a86b905-929a-11eb-9663-1c1b0d14e211';
    const receiveMoneyToSquidPayAccount = 'b1792f37-929c-11eb-9663-1c1b0d14e211';

    const buyLoad = 'edf4d5d0-9299-11eb-9663-1c1b0d14e211';

    const cashinTransactions = [
        self::posAddFunds,
        self::cashinDragonPay,
    ];
}
