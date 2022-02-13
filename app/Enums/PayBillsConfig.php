<?php

namespace App\Enums;

class PayBillsConfig
{

    const BayadCenter = 'BayadCenter';
    const ServiceFee = 15;
    const BILLS = 'c5b62dbd-95a0-11eb-8473-1c1b0d14e211';

    // Biller COde

    const billerCodes = [
        PayBillsConfig::MECOR,
        PayBillsConfig::PLDT6,
        PayBillsConfig::ETRIP,
        PayBillsConfig::HCPHL,
        PayBillsConfig::SSS01,
        PayBillsConfig::MWCOM,
        PayBillsConfig::SMART,
        PayBillsConfig::RFID1,
        PayBillsConfig::MWSIN,
        PayBillsConfig::CNVRG,
        PayBillsConfig::PWCOR,
        PayBillsConfig::HDMF1,
        PayBillsConfig::INEC1,
        PayBillsConfig::VIECO,
        PayBillsConfig::DVOLT,
        PayBillsConfig::CGNAL,
        PayBillsConfig::SKY01,
        PayBillsConfig::MBCCC,
        PayBillsConfig::BNKRD,
        PayBillsConfig::BPI00,
        PayBillsConfig::PILAM,
        PayBillsConfig::AEON1,
        PayBillsConfig::BNECO,
        PayBillsConfig::PRULI,
        PayBillsConfig::SSS03,
        PayBillsConfig::MECOP,
        PayBillsConfig::WLDVS,
        PayBillsConfig::PELC2,
        PayBillsConfig::INNOV,
        PayBillsConfig::NHA01,
        PayBillsConfig::AECOR,
        PayBillsConfig::HDMF3
    ];



    // to catch invalid account numbers
    const billerInvalidMsg = [
        PayBillsConfig::MECOR_INVALID_ACCT,
        PayBillsConfig::PLDT6_INVALID_ACCT,
        PayBillsConfig::ETRIP_INVALID_ACCT,
        PayBillsConfig::HCPHL_INVALID_ACCT,
        PayBillsConfig::MWCOM_INVALID_ACCT,
        PayBillsConfig::SMART_INVALID_ACCT,
        PayBillsConfig::MWSIN_INVALID_ACCT,
        PayBillsConfig::CNVRG_INVALID_ACCT,
        PayBillsConfig::HDMF1_INVALID_ACCT,
        PayBillsConfig::INEC1_INVALID_ACCT,
        PayBillsConfig::VIECO_INVALID_ACCT,
        PayBillsConfig::DVOLT_INVALID_ACCT,
        PayBillsConfig::CGNAL_INVALID_ACCT,
        PayBillsConfig::MBCCC_INVALID_ACCT,
        PayBillsConfig::BNKRD_INVALID_ACCT,
        PayBillsConfig::BPI00_INVALID_ACCT,
        PayBillsConfig::PILAM_INVALID_ACCT,
        PayBillsConfig::AEON1_INVALID_ACCT,
        PayBillsConfig::BNECO_INVALID_ACCT,
        PayBillsConfig::SSS03_INVALID_ACCT,
        PayBillsConfig::MECOP_INVALID_ACCT,
        PayBillsConfig::NHA01_INVALID_ACCT,
        PayBillsConfig::AECOR_INVALID_ACCT,
        PayBillsConfig::HDMF3_INVALID_ACCT
        // PayBillsConfig::WLDVS_INVALID_ACCT,  no validation for invalid account 
        // PayBillsConfig::PELC2_INVALID_ACCT,  no validation for invalid account  
    ];


    const MECOR = 'MECOR';
    const PLDT6 = 'PLDT6';
    const ETRIP = 'ETRIP';
    const HCPHL = 'HCPHL';
    const SSS01 = 'SSS01';
    const MWCOM = 'MWCOM';
    const SMART = 'SMART';
    const RFID1 = 'RFID1';
    const MWSIN = 'MWSIN';
    const CNVRG = 'CNVRG';
    const PWCOR = 'PWCOR';
    const HDMF1 = 'HDMF1';
    const INEC1 = 'INEC1';
    const VIECO = 'VIECO';
    const DVOLT = 'DVOLT';
    const CGNAL = 'CGNAL';
    const SKY01 = 'SKY01';
    const MBCCC = 'MBCCC';
    const BNKRD = 'BNKRD';
    const BPI00 = 'BPI00';
    const PILAM = 'PILAM';
    const AEON1 = 'AEON1';
    const BNECO = 'BNECO';
    const PRULI = 'PRULI';
    const SSS03 = 'SSS03';
    const MECOP = 'MECOP';
    const WLDVS = 'WLDVS';
    const PELC2 = 'PELC2';
    const INNOV = 'INNOV';
    const NHA01 = 'NHA01';
    const AECOR = 'AECOR';
    const HDMF3 = 'HDMF3';

    // invalid account numbers

    const MECOR_INVALID_ACCT = "Oh no! We can't find this Customer Account Number. Would you mind checking again? You'll find your 10-digit Meralco CAN on the lower left portion of your latest Meralco bill."; // code 3
    const PLDT6_INVALID_ACCT = "AccountNo is invalid.";   // code 26
    const ETRIP_INVALID_ACCT = "NOT_FOUND"; // "data": "NOT_FOUND"
    const HCPHL_INVALID_ACCT = "Invalid Account Number";  // code 26
    const MWCOM_INVALID_ACCT = "ContractAccountNumber is invalid."; // code 26
    const SMART_INVALID_ACCT =  "AccountNo is invalid."; // code 26
    const MWSIN_INVALID_ACCT =  "AccountNo is invalid."; // code 26
    const CNVRG_INVALID_ACCT = "Invalid customer"; // code 26
    const HDMF1_INVALID_ACCT = "RC002|The Reference number/ID provided is not valid."; // code 26
    const INEC1_INVALID_ACCT = "AcountNo not found."; // code 26
    const VIECO_INVALID_ACCT = "AccountNo is invalid."; // code 26
    const DVOLT_INVALID_ACCT = "AccountNo is invalid."; // code 26
    const CGNAL_INVALID_ACCT = "AccountNo is invalid."; // code 26
    const MBCCC_INVALID_ACCT = "The account number is not supported by the bank."; // code 27
    const BNKRD_INVALID_ACCT = "The account number is not supported by the bank."; // code 27
    const BPI00_INVALID_ACCT = "The account number is not supported by the bank."; // code 27
    const PILAM_INVALID_ACCT = "AccountNo is invalid."; // code 26
    const AEON1_INVALID_ACCT = "AccountNo is invalid."; //  "message": "Endpoint request timed out"
    const BNECO_INVALID_ACCT = "AccountNo is invalid."; // code 26
    const SSS03_INVALID_ACCT = "SS # is invalid."; // code 26
    const MECOP_INVALID_ACCT = 'Subscriber not found'; // code 26

    const AECOR_INVALID_ACCT = 'Please provide the correct A T M Reference Number.'; // code 34
    const HDMF3_INVALID_ACCT = 'Please provide the correct Account No.'; // code 34
    const NHA01_INVALID_ACCT = 'Please provide the correct B I N.'; // code 34
    // request time out
    const endpointRequestTimeOut = 'Endpoint request timed out.';
}


// ANGELES CITY WATER DISTRICT                  Does not have data
// ANGELES ELECTRIC CORPORATION                 DONE
// CITY OF SAN FERNANDO WATER DISTRICT          Does not have data
// PAMPANGA II ELECTRIC COOPERATIVE, INC.       DONE
// DEPARTMENT OF FOREIGN AFFAIRS                Does not have data
// NATIONAL BUREAU OF INVESTIGATION             Does not have data
// NATIONAL HOUSING AUTHORITY                   DONE
// Innove                                       There was an error in generating the access token.Check to be sure you are using the correct client key and secret values. 
// BENECO,                                      Undefined index:DATA                     
// PRIME WATER,                                 Does not have data      
// CLEARWATER,                                  Does not have data    
// WORLD VISION,                                DONE
// AEON                                         DONE   
// Baguio Water District,                       Does not have data   
// PAG-IBIG                                     DONE
// PAG-IBIG OFW                                 DONE  

