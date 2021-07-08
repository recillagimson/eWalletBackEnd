<?php

namespace Database\Seeders;

use App\Models\SecurityBank\PesoNetBank;
use Illuminate\Database\Seeder;

class PesoNetBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PesoNetBank::truncate();
        $banks = [
            [
                "bank_name" => "AL-AMANAH ISLAMIC BANK",	
                "bank_bic" => "AIIPPHM1",
            ],
            [
                "bank_name" => "ALLBANK , INC. (A THRIFT BANK)", 	
                "bank_bic" => "ALKBPHM2",
            ],
            [
                "bank_name" => "ASIA UNITED BANK", 	
                "bank_bic" => "AUBKPHMM",
            ],
            [
                "bank_name" => "AUSTRALIA & NEW ZEALAND BANK", 	
                "bank_bic" => "ANZBPHMX",
            ],
            [
                "bank_name" => "BANCO DE ORO UNIBANK, INC.", 	
                "bank_bic" => "BNORPHMM",
            ],
            [
                "bank_name" => "BANGKOK BANK PUBLIC CO., LTD.", 	
                "bank_bic" => "BKKBPHMM",
            ],
            [
                "bank_name" => "BANK OF AMERICA, NAT'L. ASS'N.", 
                "bank_bic" => "BOFAPH2",
            ],
            [
                "bank_name" => "BANK OF CHINA",	
                "bank_bic" => "BKCHPHMM",
            ],
            [
                "bank_name" => "BANK OF COMMERCE",	
                "bank_bic" => "PABIPHMM",
            ],
            [
                "bank_name" => "BANK OF FLORIDA",	
                "bank_bic" => "BORRPHM1",
            ],
            [
                "bank_name" => "BANK OF THE PHILIPPINE ISLANDS",	
                "bank_bic" => "BOPIPHMM",
            ],
            [
                "bank_name" => "BDO NETWORK BANK",	
                "bank_bic" => "ONNRPHM1",
            ],
            [
                "bank_name" => "CHINA BANK SAVINGS",	
                "bank_bic" => "CHSVPHM1",
            ],
            [
                "bank_name" => "CHINA BANKING CORPORATION",	
                "bank_bic" => "CHBKPHMM",
            ],
            [
                "bank_name" => "CIMB Bank",	
                "bank_bic" => "CIPHPHM1",
            ],
            [
                "bank_name" => "CITIBANK, N. A.",	
                "bank_bic" => "CITIPHMX",
            ],
            [
                "bank_name" => "CTBC BANK (PHILIPPINES) CORP.",	
                "bank_bic" => "CTCBPHMM",
            ],
            [
                "bank_name" => "DCPAY PHILIPPINES, INC.",
                "bank_bic" => "DCPHPHM1",
            ],
            [
                "bank_name" => "DEUTSCHE BANK",	
                "bank_bic" => "DEUTPHMM",
            ],
            [
                "bank_name" => "DEVT. BANK OF THE PHILIPPINES",	
                "bank_bic" => "DBPHPHMM",
            ],
            [
                "bank_name" => "DUNGGANON BANK, INC.",	
                "bank_bic" => "DUMTPHM1",
            ],
            [
                "bank_name" => "EAST-WEST BANKING CORPORATION",	
                "bank_bic" => "EWBCPHMM",
            ],
            [
                "bank_name" => "EQUICOM SAVINGS BANK, INC.",	
                "bank_bic" => "EQSNPHM1",
            ],
            [
                "bank_name" => "FIRST CONSOLIDATED BANK",	
                "bank_bic" => "FIOOPHM1",
            ],
            [
                "bank_name" => "HK AND SHANGHAI BANKING CORP.",	
                "bank_bic" => "HSBCPHMM",
            ],
            [
                "bank_name" => "INDUSTRIAL BANK OF KOREA - MANILA",	
                "bank_bic" => "IBKOPHMM",
            ],
            [
                "bank_name" => "ING BANK N.V.",	
                "bank_bic" => "INGBPHMMRTL",
            ],
            [
                "bank_name" => "JPMORGAN CHASE BANK",	
                "bank_bic" => "CHASPHMM",
            ],
            [
                "bank_name" => "KEB HANA BANK",	
                "bank_bic" => "KOEXPHMM",
            ],
            [
                "bank_name" => "LAND BANK OF THE PHILIPPINES",	
                "bank_bic" => "TLBPPHMM",
            ],
            [
                "bank_name" => "MALAYAN BANK",	
                "bank_bic" => "MAARPHM1",
            ],
            [
                "bank_name" => "MAYBANK PHILS.,INC.",	
                "bank_bic" => "MBBEPHMM",
            ],
            [
                "bank_name" => "MEGA INTL COMML BANK CO. LTD",	
                "bank_bic" => "ICBCPHMM",
            ],
            [
                "bank_name" => "METROPOLITAN BANK AND TRUST CO",	
                "bank_bic" => "MBTCPHMM",
            ],
            [
                "bank_name" => "MIZUHO BANK,LTD.",	
                "bank_bic" => "MHCBPHMM",
            ],
            [
                "bank_name" => "MUFG BANK, LTD",	
                "bank_bic" => "BOTKPHMM",
            ],
            [
                "bank_name" => "PAYMAYA PHILIPPINES, INC.",	
                "bank_bic" => "PAPHPHM1",
            ],
            [
                "bank_name" => "PHIL. BANK OF COMMUNICATIONS", 	
                "bank_bic" => "CPHIPHMM",
            ],
            [
                "bank_name" => "PHILIPPINE BUSINESS BANK", 	
                "bank_bic" => "PPBUPHMM",
            ],
            [
                "bank_name" => "PHILIPPINE NATIONAL BANK", 	
                "bank_bic" => "PNBMPHMM",
            ],
            [
                "bank_name" => "PHILIPPINE SAVINGS BANK", 	
                "bank_bic" => "PHSBPHMM",
            ],
            [
                "bank_name" => "PHILIPPINE TRUST COMPANY", 	
                "bank_bic" => "PHTBPHMM",
            ],
            [
                "bank_name" => "PHILIPPINE VETERANS BANK", 	
                "bank_bic" => "PHVBPHMM",
            ],
            [
                "bank_name" => "PRODUCERS SAVINGS BANK", 	
                "bank_bic" => "PSCOPHM1",
            ],
            [
                "bank_name" => "RIZAL COMMERCIAL BANKING CORP.", 	
                "bank_bic" => "RCBCPHMM",
            ],
            [
                "bank_name" => "ROBINSONS BANK CORPORATION", 	
                "bank_bic" => "ROBPPHMQ",
            ],
            [
                "bank_name" => "RURAL BANK OF GUINOBATAN", 	
                "bank_bic" => "RUGUPHM1",
            ],
            [
                "bank_name" => "SECURITY BANK CORPORATION", 	
                "bank_bic" => "SETCPHMM",
            ],
            [
                "bank_name" => "SHINHAN BANK", 	
                
                "bank_bic" => "SHBKPHMM",
            ],
            [
                "bank_name" => "STERLING BANK OF ASIA", 	
                "bank_bic" => "STLAPH22",
            ],
            [
                "bank_name" => "SUMITOMO MITSUI BANKING CORP", 	
                "bank_bic" => "SMBCPHMM",
            ],
            [
                "bank_name" => "THE STANDARD CHARTERED BANK", 	
                "bank_bic" => "SCBLPHMM",
            ],
            [
                "bank_name" => "UNION BANK OF THE PHILIPPINES", 	
                "bank_bic" => "UBPHPHMM",
            ],
            [
                "bank_name" => "UNITED COCONUT PLANTERS BANK", 	
                "bank_bic" => "UCPBPHMM",
            ],
            [
                "bank_name" => "UNITED OVERSEAS BANK PHILS.", 	
                "bank_bic" => "UOVBPHMM",
            ],
            [
                "bank_name" => "WEALTH DEVELOPMENT BANK,INC.", 	
                "bank_bic" => "WEDVPHM1",
            ],
            [
                "bank_name" => "YUANTA SAVINGS BANK,INC.", 	
                "bank_bic" => "TYBKPHMM",
            ],
        ];

        foreach($banks as $bank) {
            PesoNetBank::create($bank);
        }
    }
}
