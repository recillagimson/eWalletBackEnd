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
        PayBillsConfig::HDMF3,
        PayBillsConfig::ADMSN,
        PayBillsConfig::ADNU1,
        PayBillsConfig::AECOR,
        PayBillsConfig::ANTEC,
        PayBillsConfig::APEC1,
        PayBillsConfig::APECS,
        PayBillsConfig::EQPMC,
        PayBillsConfig::MOLD1,
        PayBillsConfig::MSPCI,
        PayBillsConfig::ASFIN,
        PayBillsConfig::ASVCA,
        PayBillsConfig::AVONC,
        PayBillsConfig::BAYAN,
        PayBillsConfig::BLKWC,
        PayBillsConfig::BPIMS,
        PayBillsConfig::CLNK1,
        PayBillsConfig::CLPCO,
        PayBillsConfig::CRMWD,
        PayBillsConfig::CSBNK,
        PayBillsConfig::CSHLO,
        PayBillsConfig::CVMFI,
        PayBillsConfig::DASCA,
        PayBillsConfig::DCTV1,
        PayBillsConfig::ECNSS,
        PayBillsConfig::FUSEL,
        PayBillsConfig::GLOBE,
        PayBillsConfig::GNTWC,
        PayBillsConfig::ILEC2,
        PayBillsConfig::LARC1,
        PayBillsConfig::LCWD1,
        PayBillsConfig::LGNWC,
        PayBillsConfig::LOPCI,
        PayBillsConfig::LPU01,
        PayBillsConfig::MAMEM,
        PayBillsConfig::MCLAC,
        PayBillsConfig::MIICO,
        PayBillsConfig::MLIFE,
        PayBillsConfig::MNWD1,
        PayBillsConfig::OMPCC,
        PayBillsConfig::RVSCI,
        PayBillsConfig::RYLCV,
        PayBillsConfig::SEZCO,
        PayBillsConfig::SLIFE,
        PayBillsConfig::SONYL,
        PayBillsConfig::SPLAN,
        PayBillsConfig::TWDIS,
        PayBillsConfig::UBNK4,
        PayBillsConfig::UBNK7,
        PayBillsConfig::UNBNK,
        PayBillsConfig::ASLNK,
        PayBillsConfig::ASPAY,
        PayBillsConfig::BCWD1,
        PayBillsConfig::BLKWC,
        PayBillsConfig::BPWWI,
        PayBillsConfig::BTCO1,
        PayBillsConfig::BTCO2,
        PayBillsConfig::CARFP,
        PayBillsConfig::CARHS,
        PayBillsConfig::CARWD,
        PayBillsConfig::CDOWD,
        PayBillsConfig::CELCO,
        PayBillsConfig::CLCTS,
        PayBillsConfig::CLIFE,
        PayBillsConfig::EPLAN,
        PayBillsConfig::ESBNK,
        PayBillsConfig::FINAS,
        PayBillsConfig::GHWSI,
        PayBillsConfig::GLDFI,
        PayBillsConfig::GREPA,
        PayBillsConfig::HMRKI,
        PayBillsConfig::HWMCS,
        PayBillsConfig::ILECO,
        // PayBillsConfig::ISLC1,
        PayBillsConfig::LAZAE,
        PayBillsConfig::LEYC2,
        PayBillsConfig::LIFE1,
        PayBillsConfig::LUELC,
        PayBillsConfig::MAREC,
        PayBillsConfig::MCARE,
        PayBillsConfig::MCWD1,
        // PayBillsConfig::MKLGU, no data
        PayBillsConfig::MMDA1,
        PayBillsConfig::MPLAN,
        PayBillsConfig::MVCOR,
        // PayBillsConfig::NBI02, no data
        PayBillsConfig::NORWD,
        PayBillsConfig::PHLT1,
        PayBillsConfig::PNCO1,
        PayBillsConfig::PRXCL,
        PayBillsConfig::RADIO,
        PayBillsConfig::RCTEL,
        // PayBillsConfig::RTI01,
        PayBillsConfig::SJEC1,
        PayBillsConfig::SKYAF,
        PayBillsConfig::SLWI1,
        // PayBillsConfig::SSS02, to follow
        PayBillsConfig::STICO,
        PayBillsConfig::STLCW,
        PayBillsConfig::STMWD,
        PayBillsConfig::SWSCO,
        PayBillsConfig::TRBNK,
          PayBillsConfig::TRBNK,

        





    ];



    // to catch invalid account numbers
    const billerInvalidMsg = [
        PayBillsConfig::MECOR_INVALID_ACCT,
        PayBillsConfig::PLDT6_INVALID_ACCT,
        PayBillsConfig::ETRIP_INVALID_ACCT,
        PayBillsConfig::HCPHL_INVALID_ACCT,
        // SSS01
        PayBillsConfig::MWCOM_INVALID_ACCT,
        PayBillsConfig::SMART_INVALID_ACCT,
        // RFID1
        PayBillsConfig::MWSIN_INVALID_ACCT,
        PayBillsConfig::CNVRG_INVALID_ACCT,
        // PWCOR
        PayBillsConfig::HDMF1_INVALID_ACCT,
        PayBillsConfig::INEC1_INVALID_ACCT,
        PayBillsConfig::VIECO_INVALID_ACCT,
        PayBillsConfig::DVOLT_INVALID_ACCT,
        PayBillsConfig::CGNAL_INVALID_ACCT,
        // SKY01
        PayBillsConfig::MBCCC_INVALID_ACCT,
        PayBillsConfig::BNKRD_INVALID_ACCT,
        PayBillsConfig::BPI00_INVALID_ACCT,
        PayBillsConfig::PILAM_INVALID_ACCT,
        PayBillsConfig::AEON1_INVALID_ACCT,
        PayBillsConfig::BNECO_INVALID_ACCT,
        // PRULI
        PayBillsConfig::SSS03_INVALID_ACCT,
        PayBillsConfig::MECOP_INVALID_ACCT,
        // WLDVS 
        // PELC2
        // INNOV
        PayBillsConfig::NHA01_INVALID_ACCT,
        PayBillsConfig::HDMF3_INVALID_ACCT,
        // ADMSN
        // ADNU1
        PayBillsConfig::AECOR_INVALID_ACCT,
        PayBillsConfig::ANTEC_OVERDUE,
        // APEC1
        // APECS
        // EQPMC
        // MOLD1
        // MSPCI
        // ASFIN
        // ASVCA
        // AVONC
        // BAYAN
        // BLKWC
        // BPIMS
        PayBillsConfig::CSBNK_INVALID_ACCT,
        PayBillsConfig::CSHLO_INVALID_ACCT,
        // CLNK1
        // CLPCO
        // CRMWD

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
    const HDMF3 = 'HDMF3';
    const ADMSN = 'ADMSN';
    const ADNU1 = 'ADNU1';
    const AECOR = 'AECOR';
    const ANTEC = 'ANTEC';
    const APEC1 = 'APEC1';
    const APECS = 'APECS';
    const EQPMC = 'EQPMC';
    const MOLD1 = 'MOLD1';
    const MSPCI = 'MSPCI';
    const ASFIN = 'ASFIN';
    const ASVCA = 'ASVCA';
    const AVONC = 'AVONC';
    const BAYAN = 'BAYAN';
    const BLKWC = 'BLKWC';
    const BPIMS = 'BPIMS';
    const CLNK1 = 'CLNK1';
    const CLPCO = 'CLPCO';
    const CRMWD = 'CRMWD';
    const CSBNK = 'CSBNK';
    const CSHLO = 'CSHLO';
    const CVMFI = 'CVMFI';
    const DASCA = 'DASCA';
    const DCTV1 = 'DCTV1';
    const ECNSS = 'ECNSS';
    const FUSEL = 'FUSEL';
    const GLOBE = 'GLOBE';
    const GNTWC = 'GNTWC';
    const ILEC2 = 'ILEC2';
    const LARC1 = 'LARC1';
    const LCWD1 = 'LCWD1';
    const LGNWC = 'LGNWC';
    const LOPCI = 'LOPCI';
    const LPU01 = 'LPU01';
    const MAMEM = 'MAMEM';
    const MCLAC = 'MCLAC';
    const MIICO = 'MIICO';
    const MLIFE = 'MLIFE';
    const MNWD1 = 'MNWD1';
    const OMPCC = 'OMPCC';
    const RVSCI = 'RVSCI';
    const RYLCV = 'RYLCV';
    const SEZCO = 'SEZCO';
    const SLIFE = 'SLIFE';
    const SONYL = 'SONYL';
    const SPLAN = 'SPLAN';
    const TWDIS = 'TWDIS';
    const UBNK4 = 'UBNK4';
    const UBNK7 = 'UBNK7';
    const UNBNK = 'UNBNK';
    const ASLNK = 'ASLNK';
    const ASPAY = 'ASPAY';
    const BCWD1 = 'BCWD1';
    // const BLKWC = 'BLKWC';
    const BPWWI = 'BPWWI';
    const BTCO1 = 'BTCO1';
    const BTCO2 = 'BTCO2';
    const CARFP = 'CARFP';
    const CARHS = 'CARHS';
    const CARWD = 'CARWD';
    const CDOWD = 'CDOWD';
    const CELCO = 'CELCO';
    const CLCTS = 'CLCTS';
    const CLIFE = 'CLIFE';
    const EPLAN = 'EPLAN';
    const ESBNK = 'ESBNK';
    const FINAS = 'FINAS';
    const GHWSI = 'GHWSI';
    const GLDFI = 'GLDFI';
    const GREPA = 'GREPA';
    const HMRKI = 'HMRKI';
    const HWMCS = 'HWMCS';
    const ILECO = 'ILECO';
    // const ISLC1 = 'ISLC1';
    const LAZAE = 'LAZAE';
    const LEYC2 = 'LEYC2';
    const LIFE1 = 'LIFE1';
    const LUELC = 'LUELC';
    const MAREC = 'MAREC';
    const MCARE = 'MCARE';
    const MCWD1 = 'MCWD1';
    // const MKLGU = 'MKLGU'; no data  
    const MMDA1 = 'MMDA1';
    const MPLAN = 'MPLAN';  
    const MVCOR = 'MVCOR';
    // const NBI02 = 'NBI02'; no data
    const NORWD = 'NORWD';
    const PHLT1 = 'PHLT1';
    const PNCO1 = 'PNCO1';
    const PRXCL = 'PRXCL';
    const RADIO = "RADIO";
    const RCTEL = 'RCTEL';
    // const RTI01 = 'RTI01';
    const SJEC1 = 'SJEC1';
    const SKYAF = 'SKYAF';
    const SLWI1 = 'SLWI1';
    // const SSS02 = 'SSS02'; to follow
    const STICO = 'STICO';
    const STLCW = 'STLCW';
    const STMWD = "STMWD";
    const SWSCO = 'SWSCO';
    const TRBNK = 'TRBNK';



    // invalid account numbers

    const MECOR_INVALID_ACCT = "Oh no! We can't find this Customer Account Number. Would you mind checking again? You'll find your 10-digit Meralco CAN on the lower left portion of your latest Meralco bill."; // code 3
    const PLDT6_INVALID_ACCT = "AccountNo is invalid.";   // code 26
    const ETRIP_INVALID_ACCT = "NOT_FOUND"; // "data": "NOT_FOUND"
    const HCPHL_INVALID_ACCT = "Invalid Account Number";  // code 26
    // SSS01
    const MWCOM_INVALID_ACCT = "ContractAccountNumber is invalid."; // code 26
    const SMART_INVALID_ACCT =  "AccountNo is invalid."; // code 26
    // RFID1
    const MWSIN_INVALID_ACCT =  "AccountNo is invalid."; // code 26
    const CNVRG_INVALID_ACCT = "Invalid customer"; // code 26
    // PWCOR
    const HDMF1_INVALID_ACCT = "RC002|The Reference number/ID provided is not valid."; // code 26
    const INEC1_INVALID_ACCT = "AcountNo not found."; // code 26
    const VIECO_INVALID_ACCT = "AccountNo is invalid."; // code 26
    const DVOLT_INVALID_ACCT = "AccountNo is invalid."; // code 26
    const CGNAL_INVALID_ACCT = "AccountNo is invalid."; // code 26
    // SKY01
    const MBCCC_INVALID_ACCT = "The account number is not supported by the bank."; // code 27
    const BNKRD_INVALID_ACCT = "The account number is not supported by the bank."; // code 27
    const BPI00_INVALID_ACCT = "The account number is not supported by the bank."; // code 27
    const PILAM_INVALID_ACCT = "AccountNo is invalid."; // code 26
    const AEON1_INVALID_ACCT = "AccountNo is invalid."; //  "message": "Endpoint request timed out"
    const BNECO_INVALID_ACCT = "AccountNo is invalid."; // code 26
    // PRULI
    const SSS03_INVALID_ACCT = "SS # is invalid."; // code 26
    const MECOP_INVALID_ACCT = 'Subscriber not found'; // code 26
    // WLDVS 
    // PELC2
    // INNOV
    const NHA01_INVALID_ACCT = 'Please provide the correct B I N.'; // code 34
    const HDMF3_INVALID_ACCT = 'Please provide the correct Account No.'; // code 34
    // ADMSN
    // ADNU1
    const AECOR_INVALID_ACCT = 'Please provide the correct A T M Reference Number.'; // code 34
    const ANTEC_OVERDUE = 'Please provide the correct B I N.'; // code 34
    // APEC1
    // APECS
    // EQPMC
    // MOLD1
    // MSPCI
    // ASFIN
    // ASVCA
    // AVONC
    // BAYAN
    // BLKWC
    // BPIMS
    // CLNK1
    // CLPCO
    // CRMWD
    const CSBNK_INVALID_ACCT = 'The account number is not valid.'; // code 34    
    const CSHLO_INVALID_ACCT = "The Loan ID can't be found. Please provide the correct Loan ID.";
    // CVMFI
    // DASCA
    // DCTV1
    const ECNSS_INVALID_ACCT = "Please provide the correct Reference No."; // code 34

    
    // request time out
    const endpointRequestTimeOut = 'This biller does not accept overdue bill. Please pay directly to the biller.';
    
}


// DONE

// Angeles Electric Corporation
// Pampanga II Electric Cooperative, Inc.
// National Housing Authority
// World Vision
// Pag-ibig  
// Pag-ibig OFW 
// Adamson University
// Ateneo De Naga University
// Antique Electric
// Albay Power
// Apec Schools
// Spring Clear Equiland
// Moldex Realty Inc
// M Spectrum
// Phil. Life Financial Assurance
// Asian Vision
// Avon
// Bayantel
// Bulakan Water Company Inc
// BPI MS Insurance Corp
// Cablelink
// Cotabato Light
// Carmona Water District.
// City Saving Bank
// Cashalo
// CVM Finance
// DASCA Cable Services Inc.
// DCTV Cable Network Broadband

// EQPMC
// MOLD1
// MSPCI
// ASVCA
// AVONC
// BAYAN
// BLKWC
// BPIMS
// CLNK1
// CLPCO
// CRMWD
// CSBNK


// CSHLO
// CVMFI
// DASCA
// DCTV1
// ECNSS
// FUSEL
// GLOBE
// GNTWC
// ILEC2
// LARC1
// LCWD1
// LGNWC
// LOPCI
// LPU01
// MAMEM
// MCLAC
// MIICO
// MLIFE
// MNWD1
// OMPCC
// RVSCI
// RYLCV
// SEZCO
// SLIFE
// SONYL
// SPLAN
// TWDIS
// UBNK4
// UBNK7
// UNBNK

// EZ BILLERS TO ADD

// ASLNK  -   with error
// ASPAY
// BCWD1
// BLKWC
// BPWWI
// BTCO1
// BTCO2
// CARFP
// CARHS
// CARWD
// CDOWD
// CELCO
// CGNAL
// CLCTS
// CLIFE
// EPLAN
// ESBNK
// FINAS
// GHWSI
// GLDFI
// GREPA
// HMRKI
// HWMCS
// ILEC2
// ILECO
// ISLC1
// LAZAE
// LEYC2
// LIFE1
// LPU01
// LUELC
// MAREC
// MCARE
// MCWD1
// MKLGU
// MMDA1
// MPLAN
// MVCOR
// NBI02
// NBMAP
// NHMFC
// NORWD
// PALEX
// PHLTH
// PHLT1
// PILTS
// PNCO1
// POEA1
// PRXCL
// RADIO
// RCTEL
// RTI01
// SJEC1
// SKYAF
// SLWI1
// SSS02
// STICO
// STLCW
// STMWD
// SWSCO
// TRBNK


