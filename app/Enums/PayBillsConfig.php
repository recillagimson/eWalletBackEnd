<?php

namespace App\Enums;

class PayBillsConfig
{

    const BayadCenter = 'BayadCenter';
    const ServiceFee = 15;
    const BILLS = 'c5b62dbd-95a0-11eb-8473-1c1b0d14e211';

    // Biller COde

    const billerCodes = [
        PayBillsConfig::MWCOM,
        PayBillsConfig::MECOR,
        PayBillsConfig::MWSIN,
        //PayBillsConfig::RFID1,
        PayBillsConfig::ETRIP,
        PayBillsConfig::SMART,
        PayBillsConfig::SSS03,
        PayBillsConfig::PRULI,
        PayBillsConfig::MBCCC,
        PayBillsConfig::BPI00,
        PayBillsConfig::UNBNK,
        PayBillsConfig::SPLAN,
        PayBillsConfig::PILAM,
        // PayBillsConfig::ADMSN,
        PayBillsConfig::UBNK4,
        PayBillsConfig::ASLNK,
        PayBillsConfig::CNVRG,
        // PayBillsConfig::AEON1,
        PayBillsConfig::BNECO,
        PayBillsConfig::AECOR,
        // PayBillsConfig::LAZAE,
        // PayBillsConfig::DFA01,
        // PayBillsConfig::POEA1,
        // PayBillsConfig::SSS01,
        // PayBillsConfig::SSS02,
        // PayBillsConfig::SKY01,
        PayBillsConfig::MCARE,
        PayBillsConfig::BNKRD,
    ];

    const MWCOM = 'MWCOM';
    const MECOR = 'MECOR';
    const MWSIN = 'MWSIN';
    const RFID1 = 'RFID1';
    const ETRIP = 'ETRIP';
    const SMART = 'SMART';
    const SSS03 = 'SSS03';
    const PRULI = 'PRULI';
    const MBCCC = 'MBCCC';
    const BPI00 = 'BPI00';
    const UNBNK = 'UNBNK';
    const SPLAN = 'SPLAN';
    const PILAM = 'PILAM';
    const ADMSN = 'ADMSN';
    const UBNK4 = 'UBNK4';
    const ASLNK = 'ASLNK';
    const CNVRG = 'CNVRG';
    const AEON1 = 'AEON1';
    const BNECO = 'BNECO';
    const AECOR = 'AECOR';
    const LAZAE = 'LAZAE';
    const DFA01 = 'DFA01';
    const POEA1 = 'POEA1';
    const SSS01 = 'SSS01';
    const SSS02 = 'SSS02';
    const SKY01 = "SKY01";
    const MCARE = "MCARE";
    const BNKRD = "BNKRD";

}
