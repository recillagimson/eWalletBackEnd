<?php


namespace App\Enums;


class TransactionCategoryIds
{
    const send2BankInstaPay = '0ec41025-9131-11eb-b44f-1c1b0d14e211';
    const send2BankPesoNet = '0ec432e7-9131-11eb-b44f-1c1b0d14e211';
    const send2BankUBP = '0ec432e7-9131-11eb-b44f-1c1b0d14e211';

    const posAddFunds = '0ec436e0-9131-11eb-b44f-1c1b0d14e211';
    const cashinDragonPay = '0ec43457-9131-11eb-b44f-1c1b0d14e211';

    const cashinTransactions = [
        self::posAddFunds,
        self::cashinDragonPay,
    ];
}
