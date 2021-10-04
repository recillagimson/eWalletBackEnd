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
    const AddMoneyViaOTCECPay = 'ADD_MONEY_OTC_ECPAY';
    const AddMoneyViaSquidPay = 'ADD_MONEY_SQUIDPAY';
    const sendMoneyUBPDirect = 'SEND_MONEY_UBP_DIRECT';

    const send2BankInstapay = 'SEND2BANK_INSTAPAY';
    const send2BankPesonet = 'SEND2BANK_PESONET';

    const updateProfile = 'UPDATE_PROFILE';
    const upgradeToSilver = 'UPGRADE_TO_SILVER';
    const upgradeToBronze = 'UPDATE_BRONZE';

    const uploadSelfiePhoto = 'UPLOAD_SELFIE_PHOTO';
    const uploadIdPhoto = 'UPLOAD_ID_PHOTO';

    const AddMoneyViaWebBanksUpbDirect = 'ADD_MONEY_WEB_BANK_UPB_DIRECT';
    const AddMoneyViaCebuana = 'ADD_MONEY_WEB_BANK_CEBUANA';
}
