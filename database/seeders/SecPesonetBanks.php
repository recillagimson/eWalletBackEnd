<?php

namespace Database\Seeders;

use App\Models\ProviderBank;
use Illuminate\Database\Seeder;

class SecPesonetBanks extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = [
            ['provider' => 'secbank-pesonet', 'name' => 'AL-AMANAH ISLAMIC BANK', 'code' => 'AIIPPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'ALLBANK , INC. (A THRIFT BANK)', 'code' => 'ALKBPHM2'],
            ['provider' => 'secbank-pesonet', 'name' => 'ASIA UNITED BANK', 'code' => 'AUBKPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'AUSTRALIA & NEW ZEALAND BANK', 'code' => 'ANZBPHMX'],
            ['provider' => 'secbank-pesonet', 'name' => 'BANCO DE ORO UNIBANK, INC.', 'code' => 'BNORPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'BANGKOK BANK PUBLIC CO., LTD.', 'code' => 'BKKBPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'BANK OF AMERICA, NAT\'L. ASS\'N.', 'code' => 'BOFAPH2'],
            ['provider' => 'secbank-pesonet', 'name' => 'BANK OF CHINA', 'code' => 'BKCHPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'BANK OF COMMERCE', 'code' => 'PABIPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'BANK OF FLORIDA', 'code' => 'BORRPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'BANK OF THE PHILIPPINE ISLANDS', 'code' => 'BOPIPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'BDO NETWORK BANK', 'code' => 'ONNRPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'CHINA BANK SAVINGS', 'code' => 'CHSVPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'CHINA BANKING CORPORATION', 'code' => 'CHBKPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'CIMB Bank', 'code' => 'CIPHPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'CITIBANK, N. A.', 'code' => 'CITIPHMX'],
            ['provider' => 'secbank-pesonet', 'name' => 'CTBC BANK (PHILIPPINES] CORP.', 'code' => 'CTCBPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'DCPAY PHILIPPINES, INC.', 'code' => 'DCPHPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'DEUTSCHE BANK', 'code' => 'DEUTPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'DEVT. BANK OF THE PHILIPPINES', 'code' => 'DBPHPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'DUNGGANON BANK, INC.', 'code' => 'DUMTPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'EAST-WEST BANKING CORPORATION', 'code' => 'EWBCPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'EQUICOM SAVINGS BANK, INC.', 'code' => 'EQSNPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'FIRST CONSOLIDATED BANK', 'code' => 'FIOOPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'HK AND SHANGHAI BANKING CORP.', 'code' => 'HSBCPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'INDUSTRIAL BANK OF KOREA - MANILA', 'code' => 'IBKOPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'ING BANK N.V.', 'code' => 'INGBPHMMRTL'],
            ['provider' => 'secbank-pesonet', 'name' => 'JPMORGAN CHASE BANK', 'code' => 'CHASPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'KEB HANA BANK', 'code' => 'KOEXPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'LAND BANK OF THE PHILIPPINES', 'code' => 'TLBPPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'MALAYAN BANK', 'code' => 'MAARPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'MAYBANK PHILS.,INC.', 'code' => 'MBBEPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'MEGA INTL COMML BANK CO. LTD', 'code' => 'ICBCPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'METROPOLITAN BANK AND TRUST CO', 'code' => 'MBTCPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'MIZUHO BANK,LTD.', 'code' => 'MHCBPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'MUFG BANK, LTD', 'code' => 'BOTKPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'PAYMAYA PHILIPPINES, INC.', 'code' => 'PAPHPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'PHIL. BANK OF COMMUNICATIONS', 'code' => 'CPHIPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'PHILIPPINE BUSINESS BANK', 'code' => 'PPBUPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'PHILIPPINE NATIONAL BANK', 'code' => 'PNBMPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'PHILIPPINE SAVINGS BANK', 'code' => 'PHSBPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'PHILIPPINE TRUST COMPANY', 'code' => 'PHTBPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'PHILIPPINE VETERANS BANK', 'code' => 'PHVBPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'PRODUCERS SAVINGS BANK', 'code' => 'PSCOPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'RIZAL COMMERCIAL BANKING CORP.', 'code' => 'RCBCPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'ROBINSONS BANK CORPORATION', 'code' => 'ROBPPHMQ'],
            ['provider' => 'secbank-pesonet', 'name' => 'RURAL BANK OF GUINOBATAN', 'code' => 'RUGUPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'SECURITY BANK CORPORATION', 'code' => 'SETCPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'SHINHAN BANK', 'code' => 'SHBKPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'STERLING BANK OF ASIA', 'code' => 'STLAPH22'],
            ['provider' => 'secbank-pesonet', 'name' => 'SUMITOMO MITSUI BANKING CORP', 'code' => 'SMBCPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'THE STANDARD CHARTERED BANK', 'code' => 'SCBLPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'UNION BANK OF THE PHILIPPINES', 'code' => 'UBPHPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'UNITED COCONUT PLANTERS BANK', 'code' => 'UCPBPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'UNITED OVERSEAS BANK PHILS.', 'code' => 'UOVBPHMM'],
            ['provider' => 'secbank-pesonet', 'name' => 'WEALTH DEVELOPMENT BANK,INC.', 'code' => 'WEDVPHM1'],
            ['provider' => 'secbank-pesonet', 'name' => 'YUANTA SAVINGS BANK,INC.', 'code' => 'TYBKPHMM'],
        ];

        ProviderBank::insert($banks);
    }
}
