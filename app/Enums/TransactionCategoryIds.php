<?php


namespace App\Enums;


class TransactionCategoryIds
{
    const send2BankInstaPay = '0ec41025-9131-11eb-b44f-1c1b0d14e211';
    const send2BankPesoNet = '0ec432e7-9131-11eb-b44f-1c1b0d14e211';
    const send2BankUBP = '0ec432e7-9131-11eb-b44f-1c1b0d14e211';

    const posAddFunds = '0ec436e0-9131-11eb-b44f-1c1b0d14e211';
    const cashinDragonPay = '0ec43457-9131-11eb-b44f-1c1b0d14e211';
    const cashinBPI = 'edf5d5d0-9299-11eb-9663-1c1b0d14e211';
    const cashinUBP = '4bb15489-71d4-49fa-a043-e6820e0d52d2';

    const sendMoneyToSquidPayAccount = '1a86b905-929a-11eb-9663-1c1b0d14e211';
    const receiveMoneyToSquidPayAccount = 'b1792f37-929c-11eb-9663-1c1b0d14e211';

    const buyLoad = 'edf4d5d0-9299-11eb-9663-1c1b0d14e211';
    const payBills = 'c5b62dbd-95a0-11eb-8473-1c1b0d14e211';

    const drMemo = '0ec434b6-9131-11eb-b44f-1c1b0d14e211';
    const crMemo = '0ec43514-9131-11eb-b44f-1c1b0d14e211';

    const dbpSubsidy = 'edj4d5d0-9299-11eb-9663-1c1b0d14e211';

    const addMoneyCebuana = 'edj4d5d0-9255-11eb-9663-1c1b0d14e218';

    const adMoneyEcPay = 'edj4d5d0-9234-11eb-9663-1c1b0d14e218';

    const cashinTransactions = [
        self::posAddFunds,
        self::cashinDragonPay,
    ];
}
