<?php

namespace App\Http\Requests\PayBills;

use App\Enums\PayBillsConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Validation\Rule;
use Request;

class ValidateAccountRequest extends FormRequest
{   

    private $validationArray = [
        // 1st BILLERS

        PayBillsConfig::MECOR  => [
            'account_number' => 'required|digits:10',  // code 3
            'amount' => 'required|numeric|min:5.00|max:100000.00'
        ],
        PayBillsConfig::PLDT6 => [
            'account_number' => 'required|digits:10',  // code 26
            'amount' => 'required|numeric|min:200.00|max:100000.00',
            'otherInfo.PhoneNumber' => 'required|digits:10',
            'otherInfo.Service' => 'required|in:PL,PD,PU'
        ],
        PayBillsConfig::ETRIP => [
            'account_number' => 'required|digits:12',  // "data": "NOT_FOUND"
            'amount' => 'required|numeric|min:500.00|max:100000.00'
        ],
        PayBillsConfig::HCPHL => [
            'account_number' => 'required|digits:10',  // code 26
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.Name' => "required",
            'otherInfo.PhoneNo' => "required|digits:11",
        ],
        PayBillsConfig::MWCOM => [
            'account_number' => 'required|digits:8',    // code 26
            'amount' => 'required|numeric|min:20.00|max:100000.00'
        ],
        PayBillsConfig::SMART => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.Product' =>  'required|in:b,c',
            'otherInfo.TelephoneNumber' => 'required|digits:10'
        ],
        PayBillsConfig::MWSIN => [
            'account_number' => 'required|digits:8',
            'amount' => 'required|numeric|min:20.00|max:100000.00'
        ],
        PayBillsConfig::CNVRG => [
            'account_number' => 'required|digits:13',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => 'required|max:50',
        ],
        PayBillsConfig::INEC1 => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.Name' => "required|max:100",
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ],
        PayBillsConfig::VIECO => [
            'account_number' => 'required|between:11,13',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
        ],
        PayBillsConfig::DVOLT => [
            'account_number' => 'required|between:11,13',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
        ],
        PayBillsConfig::CGNAL => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.ExternalEntityName' => 'required|in:BAYAD',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.LastName' => 'required|max:100'
        ],
        PayBillsConfig::MBCCC => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.ConsName' => 'required|max:100',
        ],
        PayBillsConfig::BNKRD => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => 'required|max:100',
            'otherInfo.BillDate' => "required|date_format:m/d/Y",
        ],
        PayBillsConfig::BPI00 => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.ConsName' => 'required|max:100',
        ],
        PayBillsConfig::PILAM => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.DueDate' => "required|date_format:m/d/Y"
        ],
        PayBillsConfig::BNECO => [
            'account_number' => 'required|digits:11',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.LastName' => "required|max:100",
            'otherInfo.FirstName' => "required|max:100",
            'otherInfo.DueDate' => "required|date_format:m/d/Y"
        ],

        PayBillsConfig::HDMF1 => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.PaymentType' => "required|in:MC,HL,MP2,ST",
            'otherInfo.ContactNo' => "required|between:7,11",
            'otherInfo.BillDate' => "required",
            'otherInfo.DueDate' => "required|date_format:m/d/Y",
        ],
        PayBillsConfig::PRULI => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => "required|max:100",
            'otherInfo.DueDate' => "required|date_format:m/d/Y",
        ],
        PayBillsConfig::RFID1 => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
        ],
        PayBillsConfig::SSS03 => [
            'account_number' => 'required|between:10,13',
            'amount' => 'required|numeric|min:1.00|max:200000.00',
            'otherInfo.PayorType' => 'required|in:I,R',
            'otherInfo.RelType' => 'required|in:LP',
            'otherInfo.LoanAccountNo' => 'required|numeric|digits:10',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MI' => 'required|max:2',
            'otherInfo.PlatformType' => 'required|in:OTC,SS'
        ],
        PayBillsConfig::MECOP => [
            'account_number' => 'required',
            'amount' => 'required|numeric|in:100.00,200.00,300.00,500.00,1000.00',
        ],
        PayBillsConfig::WLDVS => [
            'account_number' => 'required|digits:9',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => "required|max:100",
            'otherInfo.DueDate' => "required|date_format:m/Y",
            'otherInfo.Pledge' => "required|in:RD,OT"
        ],
        PayBillsConfig::PELC2 => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.DueDate' => "required|date_format:m/d/Y",
            'otherInfo.ConsumerName' => "required|max:100"
        ],    
        PayBillsConfig::INNOV => [
            'account_number' => 'required|digits:9',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.Telephone_Number' => "required|numeric|digits:10",
        ],    
        PayBillsConfig::NHA01 => [
            'account_number' => 'required|max:9',
            'amount' => 'required|numeric|min:200.00|max:100000.00',
            'otherInfo.BeneficiaryName' => "required|max:50" 
        ], 
        PayBillsConfig::HDMF3 => [
            'account_number' => 'required|between:10,20',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.PaymentType' => "required|in:MC,HL,MP2,ST",
            'otherInfo.Region' => "required",
            'otherInfo.ContactNo' => "required|between:10,20",
            'otherInfo.PeriodFrom' => "required|date_format:Y/m",
            'otherInfo.PeriodTo' => "required|date_format:Y/m",
        ], 
        PayBillsConfig::ADMSN => [
            'account_number' => 'required|digits:9',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.LastName' => 'required|max:40',
            'otherInfo.FirstName' => 'required|max:40',
            'otherInfo.MiddleName' => 'required|max:40',
            'otherInfo.PaymentType' => 'required|in:B2,DP,MT,FT',
            'otherInfo.Course' => 'required|max:10',
            'otherInfo.TotalAssessment' => 'required|min:0.00',
            'otherInfo.SchoolYear' => 'required',  // I removed the date format coz it has an error
            'otherInfo.Term' => 'required|in:1,2,3'
        ],
        PayBillsConfig::ADNU1 => [
            'account_number' => 'required|alpha_num',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.Name' => 'required|max:40',
            'otherInfo.AccountType' => 'required|in:ADNU1'
        ],
        PayBillsConfig::AECOR => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:99999.99',
            'otherInfo.DueDate' => "required|date_format:m/d/Y",
            'otherInfo.CustomerName' => 'required|max:100'
        ], 
        PayBillsConfig::ANTEC => [
            'account_number' => 'required|digits:11',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.AccountName' => 'required',
            'otherInfo.BillMonth' => 'required|date_format:m/Y'
        ],
        PayBillsConfig::APEC1 => [
            'account_number' => 'required|between:15,20',
            'amount' => 'required|numeric|min:1.00',
            "otherInfo.BillAmount" => "required",
            "otherInfo.SOA" => "required|in:1",
            "otherInfo.BillAmount" => "required|between:1,12",
            "otherInfo.BillMonth" => "required|between:1,12",
            "otherInfo.BillYear" => "required|date_format:Y",
            "otherInfo.PaymentType" => "required|in:S",
            "otherInfo.InvoiceNo" => "required",
            "otherInfo.DeliveryDate" => "required|date_format:Y-m-d",
            "otherInfo.DueDate" => "required|date_format:Y-m-d",
            "otherInfo.AccountName" => "required|max:100"
        ],
        PayBillsConfig::APECS => [
            'account_number' => 'required|max:13|alpha-dash',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.BranchCode' => 'required|in:B04,A07,C02,B03,A02,B11,B05,A09,B12,B10,C01,A08,B01,C03,A04,B09,B07,B06,D01,B02,B08,A10,A01,Others',
            'otherInfo.PaymentType' => 'required|in:AF,TF,MISC,OTH',
            'otherInfo.FirstName' => 'required:max:100',
            'otherInfo.LastName' => 'required:max:100'
        ],
        PayBillsConfig::EQPMC => [
            'account_number' => 'required|max:13|alpha-dash',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
        ],
        PayBillsConfig::MOLD1 => [
            'account_number' => 'required|digits:13',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
        ],
        PayBillsConfig::MSPCI => [
            'account_number' => 'required|numeric',
            'amount' => 'required|numeric|min:100.00',
        ],
        PayBillsConfig::ASFIN => [
            'account_number' => 'required|digits:11',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.AccountName' => 'required|max:100',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ],
        PayBillsConfig::ASVCA => [
            'account_number' => 'required|numeric|digits:6',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => 'required|max:100',
            'otherInfo.AffiliateBranch' => 'required|in:ASVCA1,ASVCA2,ASVCA3,ASVCA4,ASVCA5,ASVCA6,ASVCA7,ASVCA8,ASVCA9,ASVCA10,ASVCA11,ASVCA12,ASVCA13,ASVCA14,ASVCA15,ASVCA27,ASVCA35'
        ],
        PayBillsConfig::AVONC => [
            'account_number' => 'required|digits:13',
            'amount' => 'required|numeric|max:100000.00',
            'otherInfo.Name' => 'required|max:30',
            'otherInfo.Branch' => 'required|max:30'
        ],
        PayBillsConfig::BAYAN => [
            'account_number' => 'required|between:9,11',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.PhoneNo' => 'required|digits:10'
        ],
        PayBillsConfig::BLKWC => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
        ],
        PayBillsConfig::BPIMS => [
            'account_number' => 'required|digits_between:8,16',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.ContactNo' => "required|numeric",
        ],
        PayBillsConfig::CLNK1 => [
            'account_number' => 'required|digits:9',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.AccountName' => "required|max:100",
            'otherInfo.ContactNo' => "required|digits_between:7,11",
        ],
        PayBillsConfig::CLPCO => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.PowerCompany' => 'required|in:CLPC'
        ],
        PayBillsConfig::CRMWD => [
            'account_number' => 'required|numeric',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => "required|max:30",
            'otherInfo.DueDate' => "required|date_format:m/d/Y",
        ],
        PayBillsConfig::CSBNK => [
            'account_number' => 'required|numeric',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.ContactNo' => "required|digits:11|numeric",
        ],
        PayBillsConfig::CSHLO => [
            'account_number' => 'required|alpha-num|max:64',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.ContactNo' => 'required|numeric|digits_between:10,11',
        ],
        PayBillsConfig::CVMFI => [
            'account_number' => 'required|numeric',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => 'required|between:10,50',
        ],
        PayBillsConfig::DASCA => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.LastName' => 'max:15|required',
            'otherInfo.FirstName' => 'max:15|required',
        ],
        PayBillsConfig::DCTV1 => [
            'account_number' => 'required|between:9,11',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => 'max:30|required',
            'otherInfo.BillMonth' => 'required',
        ],
        PayBillsConfig::ECNSS => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.PayorName' => 'max:40|required',
            'otherInfo.ContactNo' => 'max:20|required',
        ],
        PayBillsConfig::FUSEL => [
            'account_number' => 'required|digits:11',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.CustomerName' => 'max:30|required',
        ],
        PayBillsConfig::GLOBE => [
            'account_number' => 'required|digits_between:8,10|numeric',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.AccountName' => 'max:100',
            'otherInfo.Telephone_Number' => 'digits_between:7,11',
            'otherInfo.BankAccountNumber' => 'digits:14',
        ],
        PayBillsConfig::GNTWC => [
            'account_number' => 'required|between:1,20',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.Name' => 'max:40|required',
            'otherInfo.ContactNo' => 'required|date_format:m/d/Y',
        ],
        PayBillsConfig::ILEC2 => [
            'account_number' => 'required|digits:9',
            'amount' => 'required',
            'otherInfo.Name' => 'max:100|required',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
        ],
        PayBillsConfig::LARC1 => [
            'account_number' => 'required|between:8,9',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => 'max:30|required',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
        ],
        PayBillsConfig::LCWD1 => [
            'account_number' => 'required|digits:9',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.BillMonth' => 'required',
            'otherInfo.AccountName' => 'required|max:30',
        ],
        PayBillsConfig::LGNWC => [
            'account_number' => 'required|digits:8|starts_with:3',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
        ],
        PayBillsConfig::LOPCI => [
            'account_number' => 'required|digits:8',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.BankPaymentCode' => 'required|digits:9|numeric',
            'otherInfo.CheckDate' => 'date_format:m/d/Y',
            'otherInfo.ClientName' => 'max:100',
        ],
        PayBillsConfig::LPU01 => [
            'account_number' => 'required|max:20',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.Campus' => 'required|in:Manila,Cavite',
            'otherInfo.StudentName' => 'required|max:100',
        ],
        PayBillsConfig::MAMEM => [
            'account_number' => 'required|digits:8',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.AccountName' => 'required|max:100',
        ],
        PayBillsConfig::MCLAC => [
            'account_number' => 'required|digits:10|numeric',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.AccountName' => 'required|max:100',
        ],
        PayBillsConfig::MIICO => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.Name' => 'required|max:40',
            
        ],
        PayBillsConfig::MLIFE => [
            'account_number' => 'required|size:9',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            
        ],
        PayBillsConfig::MNWD1 => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00|max:100000',
        ],
        PayBillsConfig::OMPCC => [
            'account_number' => 'required|alpha_num|between:1,20',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.Name' => 'required|between:1,40',
            'otherInfo.ContactNumber' => 'required|digits:11|numeric',
        ],
        PayBillsConfig::RVSCI => [
            'account_number' => 'required|size:6',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.FirstName' => 'required|max:20',
            'otherInfo.LastName' => 'required|max:20',
        ],
        PayBillsConfig::RYLCV => [
            'account_number' => 'required|digits_between:1,9',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.AccountName' => 'required|max:100',
        ],
        PayBillsConfig::SEZCO => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.PowerCompany' => 'required|in:SEZ',
        ],
        PayBillsConfig::SLIFE => [
            'account_number' => 'required|digits_between:7,10',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.ProductType' => 'required|in:SLife1,SLife2',
            'otherInfo.AccountName' => 'required|max:100',
        ],
        PayBillsConfig::SONYL => [
            'account_number' => 'required|digits:10|numeric',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
        ],
        PayBillsConfig::SPLAN => [
            'account_number' => 'required|size:15',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.PlanType' => 'required|in:P,E',
            'otherInfo.AccountName' => 'required|max:100',
        ],
        PayBillsConfig::TWDIS => [
            'account_number' => 'required|digits:6|numeric',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.Name' => 'required|max:100',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
        ],
        PayBillsConfig::UBNK4 => [
            'account_number' => 'required|between:8,15|alpha_num',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.StudentName' => 'required|between:8,30',
            'otherInfo.Branch' => 'required|max:99',
        ],
        PayBillsConfig::UBNK7 => [
            'account_number' => 'required|digits_between:2,10',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.StudentName' => 'required|between:8,30',
            'otherInfo.Campus' => 'required|in:1,2,3,4,5',
        ],
        PayBillsConfig::UNBNK => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.Service' => 'required|in:0,1',
            'otherInfo.ConsName' => 'required|max:100',
        ],

        // Start here

        PayBillsConfig::ASLNK => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MiddleName' => 'required|max:100',
            'otherInfo.LastName' => 'required|max:100',
        ],
        PayBillsConfig::ASPAY => [
            'account_number' => 'required|digits:20',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.FirstName' => 'required',
            'otherInfo.MI' => 'required',
            'otherInfo.LastName' => 'required',
            'otherInfo.BillName' => 'required',
        ],
        PayBillsConfig::BCWD1 => [
            'account_number' => 'required|digits:9',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.AccountName' => 'required|max:30',
            'otherInfo.DueDate' => 'required|date:m/d/Y',
            'otherInfo.MeterNo' => 'required|digits:9',
        ],
        // PayBillsConfig::BLKWC => [
        //     'account_number' => 'required',
        //     'amount' => 'required|numeric|min:1.00|max:100000',
        //     'otherInfo.ContactNumber' => '',
        // ],    MISSMATCHED OTHERINFO
        PayBillsConfig::BPWWI => [
            'account_number' => 'required|max:12',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.TypeOfService' => 'required|in:WB,Misc',
            'otherInfo.AccountName' => 'required|max:100',
            'otherInfo.DueDate' => 'required|date:m/d/Y',
            'otherInfo.DisconnectionDate' => 'required|date:m/d/Y',
        ],
        PayBillsConfig::BTCO1 => [
            'account_number' => 'required|digits:10|numeric',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.DueDate' => 'required|date:m/d/Y',
            'otherInfo.ConsumerName' => 'required|max:100',
            'otherInfo.BillMonth' => 'required|date_format:m/Y',
        ],
        PayBillsConfig::BTCO2 => [
            'account_number' => 'required|digits:7',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.ConsumerName' => 'required|max:100',
            'otherInfo.DueDate' => 'required|date:Y/m/d',
        ],
        PayBillsConfig::CARFP => [
            'account_number' => 'required|max:15',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MI' => 'required',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.ContactNo' => 'required|digits_between:7,11',
        ],
        PayBillsConfig::CARHS => [
            'account_number' => 'required|max:15',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MI' => 'required',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.ContactNo' => 'required|digits_between:7,11|numeric',
        ],
        PayBillsConfig::CARWD => [
            'account_number' => 'required|digits:6',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.AccountName' => 'required|max:50',
            'otherInfo.BillAmount' => 'required|numeric',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
        ],
        PayBillsConfig::CDOWD => [
            'account_number' => 'required|max:10',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.WIN' => 'required|digits:9',
            'otherInfo.AccountName' => 'required',
        ],
        PayBillsConfig::CELCO => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.ContactNumber' => 'required|digits:11',
            'otherInfo.BillingPeriod' => 'required|date_format:m/Y',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
        ],
        PayBillsConfig::CLCTS => [
            'account_number' => 'required|digits_between:8,16',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.ContactNo' => 'required|digits:11',
        ],
        PayBillsConfig::CLIFE => [
            'account_number' => 'required|size:8',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.AccountName' => 'required',
            'otherInfo.PremiumAmount' => 'required|numeric|min:1',
            'otherInfo.LoanAmount' => 'required|numeric',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
        ],
        PayBillsConfig::EPLAN => [
            'account_number' => 'required|alpha_num|between:11,13',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.AccountName' => 'required',
            'otherInfo.ProductType' => 'required|in:L,C,P,E,D',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ],
        PayBillsConfig::ESBNK => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MI' => 'required',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.ContactNo' => 'required|digits_between:7,11',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ],
        PayBillsConfig::FINAS => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MiddleName' => 'required|max:100',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.PhoneNo' => 'required|digits_between:7,15',
        ],
        PayBillsConfig::GHWSI => [
            'account_number' => 'required|max:12',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.TypeOfService' => 'required|in:Service1,Service2',
            'otherInfo.AccountName' => 'required|max:100',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.DisconnectionDate' => 'required|date_format:m/d/Y'
        ],
        PayBillsConfig::GLDFI => [
            'account_number' => 'required|max:20',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MI' => '',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.TelephoneNo' => 'required|digits_between:7,11'
        ],
        PayBillsConfig::GREPA => [
            'account_number' => 'required|between:8,10',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.ProductType' => 'required|in:Premium Billing',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.BillingNo' => 'required|numeric',
            'otherInfo.PremiumAmount' => 'required|numeric|min:1',
            'otherInfo.Name' => 'required|max:30'
        ],
        PayBillsConfig::HMRKI => [
            'account_number' => 'required|alpha_num|between:5,50',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MI' => '',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.Particular' => 'required|in:RF,EQ,PR,IN,PEN,MF,CB,MRI,UPG,WU,EU,DF,DPC,FI,HF,MA-HDMF,MISF,TF,RPT,BBL,REIN-FEE,HMF,MRI-FIRE,MC,MA-IHF,MA-HDMF PENALTY,POP',
            'otherInfo.ContactNumber' => 'required|numeric|digits_between:7,11'
        ],
        PayBillsConfig::HWMCS => [
            'account_number' => 'required|max:12',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.TypeOfService' => 'required|in:WB,Misc',
            'otherInfo.AccountName' => 'required|max:100',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.DisconnectionDate' => 'required|date_format:m/d/Y'
        ],
        PayBillsConfig::ILECO => [
            'account_number' => 'required|digits_between:4,10',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.ConsumerID' => 'required|max:10',
            'otherInfo.BillNumber' => 'required|max:10',
            'otherInfo.FirstName' => 'max:100',
            'otherInfo.MI' => '',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ],
        // PayBillsConfig::ISLC1 => [
        //     'account_number' => 'required|max:10',
        //     'amount' => 'required|numeric|min:1.00|max:100000',
        //     'otherInfo.ConsumerName' => 'required|max:30',
        //     'otherInfo.DueDate' => 'required|date_format:m/d/Y',
        //     'otherInfo.BillMonth' => 'required|date_format:m/Y'
        // ],  
        PayBillsConfig::LAZAE => [
            'account_number' => 'required|max:18',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.Name' => 'required|max:40',
            'otherInfo.ContactNo' => 'max:12'
        ],
        PayBillsConfig::LEYC2 => [
            'account_number' => 'required|size:10',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.BillMonth' => 'date_format:Ym|required',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.ContactNumber' => 'digits_between:7,11',
        ], 
        PayBillsConfig::LIFE1 => [
            'account_number' => 'required|digits:11',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.AccountName' => 'required|between:1,30',
            'otherInfo.TransactionType' => 'required|in:M,S',
            'otherInfo.SubType' => 'in:I,F,H,E,P,S,A,D|required'
        ], 
        PayBillsConfig::LUELC => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.BillNumber' => 'required|digits:14',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.AccountName' => 'required|max:50'
        ], 
        PayBillsConfig::MAREC => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.BillMonth' => 'required|date_format:m/Y',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.AccountName' => 'required|max:100',
            'otherInfo.TotalPayableAmount' => 'required|numeric',
            'otherInfo.Surcharge' => 'numeric'
        ], 
        PayBillsConfig::MCARE => [
            'account_number' => 'required|alpha_num',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.FirstName' => 'max:100|required',
            'otherInfo.MI' => 'max:2',
            'otherInfo.LastName' => 'max:100|required',
            'otherInfo.TelephoneNumber' => 'digits_between:7,11|numeric',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ], 
        PayBillsConfig::MCWD1 => [
            'account_number' => 'required|max:10',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.PreviousBillOnly' => 'in:0,1|required'
        ], 
        // PayBillsConfig::MKLGU => [
        //     'account_number' => 'required|alpha_num',
        //     'amount' => 'required|numeric|min:1.00|max:100000',
        //     'otherInfo.FirstName' => 'max:100|required',
        //     'otherInfo.MI' => 'max:2',
        //     'otherInfo.LastName' => 'max:100|required',
        //     'otherInfo.TelephoneNumber' => 'digits_between:7,11|numeric',
        //     'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        // ], no data 
        PayBillsConfig::MMDA1 => [
            'account_number' => 'required|size:11',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.Violation' => 'required|in:0,1,2',
            'otherInfo.ViolationCode' => 'required|in:000,001,001N,002,002A,002N,003,004,004N,005,006A,006B,006C,006N,006P,007,008,009A,A009,010,010B,010BN,010C,010N,010P,010TAX,011,012,013,013A,014,015A,A015,015B,B015,016,017A,A017,018,018A,018N,019,019A,019N,020,021,022,023A,023B,024,025,026,027,028,029,030,031,032,033,034,035,036,037,038,039,040,041,042,043,044,045,046,047,048,049,050,051,052,053,054,055,056,057,058,059,060,061,062,063,064,065,066,067,068A,A068,069A,A069,070,071,073,074,075,076,077,078,079,080,081,082,083,084,085,086,087,088,089,090,091,092,093,094,A094,095,096,097,098,099,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,1201,1202,1203,121,122,123,124,125,126,127,128,128J,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154A,154B,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,176,176A,176C,177,178,179,180,181,183,184,185,186,187,188,190,191,192,193,194,195,196,197,198,199,200,201,201M,201P,202R,203PRQ,203PSY,204M,205,206,207C,207C5,207E,207M,208,209,209IP,210,211,212,213,214,217,218,219,219J,220,220MKT,220PS,221,221A,222,224,225,225N,225N,226,227,227N,227D,228,229,230',
            'otherInfo.Name' => 'max:100|required',
            'otherInfo.ClearanceFee' => 'required|in:0,1',
        ],
        PayBillsConfig::MPLAN => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ],
        PayBillsConfig::MVCOR => [
            'account_number' => 'required|size:12|alpha_num',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.FirstName' => 'max:100|required',
            'otherInfo.MI' => 'max:2',
            'otherInfo.LastName' => 'max:100|required'
        ], 
        // PayBillsConfig::NBI02 => [
        //     'account_number' => 'required',
        //     'amount' => 'required|numeric|min:1.00',
        //     'otherInfo.FirstName' => 'max:100|required',
        //     'otherInfo.MI' => 'max:2',
        //     'otherInfo.LastName' => 'max:100|required'
        // ], no data
        PayBillsConfig::NORWD => [
            'account_number' => 'required|between:8,10|alpha_num',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.BillInvoiceNumber' => 'required|numeric|digits_between:8,10',
            'otherInfo.FirstName' => 'max:100|required',
            'otherInfo.MI' => 'max:2',
            'otherInfo.LastName' => 'max:100|required',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ], 
        PayBillsConfig::PHLT1 => [
            'account_number' => 'required|digits:12',
            'amount' => 'required|numeric|min:275',
            'otherInfo.MemberType' => 'required|in:PE,GE',
            'otherInfo.PeriodFrom' => 'required|date_format:m/Y',
            'otherInfo.PeriodTo' => 'required|date_format:m/Y',
            'otherInfo.SPANumber' => 'required|size:15'
        ], 
        PayBillsConfig::PNCO1 => [
            'account_number' => 'required|size:12',
            'amount' => 'required|numeric|min:1',
            'otherInfo.MeterNo' => 'required|max:15',
            'otherInfo.FirstName' => 'max:100|required',
            'otherInfo.MI' => 'max:2',
            'otherInfo.LastName' => 'max:100|required',
            'otherInfo.ExpirationDate' => 'required|date_format:m/d/Y',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ], 
        PayBillsConfig::PRXCL => [
            'account_number' => 'required|alpha_dash|max:13',
            'amount' => 'required|numeric|min:1|max:100000',
            'otherInfo.ProjectName' => 'required|in:PRXL1,PRXL2,PRXL3,PRXL4,PRXL5,PRXL6,PRXL7,PRXL8,PRXL9,PXL10,PXL11,PXL12,PXL13,PXL14,PXL15,PXL16,PXL17,PXL18,PXL11,PXL19,PXL20,PXL21,PXL22,PXL23,PXL24,PXL25',
            'otherInfo.Name' => 'required',
            'otherInfo.ApplicableMonth' => 'in:January,February,March,April,May,June,July,August,September,October,November,December|required'
        ], 
        PayBillsConfig::RADIO => [
            'account_number' => 'required|between:8,30',
            'amount' => 'required|numeric|min:1|max:100000',
            'otherInfo.ContactNo' => 'required|digits_between:7,11',
            'otherInfo.Name' => 'required|max:40'
        ], 
        PayBillsConfig::RCTEL => [
            'account_number' => 'required|digits_between:7,10',
            'amount' => 'required|numeric|min:1',
            'otherInfo.FirstName' => 'max:100|required',
            'otherInfo.MI' => 'required|max:2',
            'otherInfo.LastName' => 'max:100|required'
        ], 
        // PayBillsConfig::RTI01 => [
        //     'account_number' => 'required|custom:NOT_FOUND|damm',
        //     'amount' => 'required|numeric|min:1|max:100000',
        //     'otherInfo.FirstName' => 'max:100|required',
        //     'otherInfo.MI' => 'required|max:2',
        //     'otherInfo.LastName' => 'max:100|required'
        // ], 
        PayBillsConfig::SJEC1 => [
            'account_number' => 'required|size:10',
            'amount' => 'required|numeric|min:1',
            'otherInfo.BillMonth' => 'required|date_format:m/Y',
            'otherInfo.AccountName' => 'required|max:30',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y'
        ], 
        PayBillsConfig::SKYAF => [
            'account_number' => 'required|digits:8|alpha_num',
            'amount' => 'required|numeric|min:1',
            'otherInfo.AccountName' => 'required|between:1,100',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.Affiliate' => 'required|in:SKYAF1,SKYAF2,SKYAF3,SKYAF4,SKYAF5,SKYAF6,SKYAF7,SKYAF8,SKYAF9,SKYAF10'
        ], 
        PayBillsConfig::SLWI1 => [
            'account_number' => 'required|max:16',
            'amount' => 'required|numeric|min:1',
            'otherInfo.BillType' => 'required|in:W,D,M',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.AccountName' => 'required|max:100',
            'otherInfo.WaterDue' => 'required|numeric',
            'otherInfo.AssociationFee' => 'required|numeric'
        ], 
        // PayBillsConfig::SSS02 => [
        //     'account_number' => 'required|size:14',
        //     'amount' => 'required|numeric|min:1',
        //     'otherInfo.PaymentType' => 'in:I,R',
        //     'otherInfo.LoanType' => 'in:SL,CL,EL,EDL,SIL,SLE',
        //     'otherInfo.PlatformType' => 'required|in:OTC,SS',
        //     'otherInfo.CountryCode' => 'required_if:otherInfo.PlatformType,SS|size:3'
        // ],  to follow
        PayBillsConfig::STICO => [
            'account_number' => 'required|size:11',
            'amount' => 'required|numeric|min:1',
            'otherInfo.FirstName' => 'required',
            'otherInfo.MiddleName' => 'required',
            'otherInfo.LastName' => 'required',
            'otherInfo.SchoolsCode' => 'required|in:037,137,005,131,02,047,066,001,38,35,004,007,008,021,079,053,013,012,074,070,141,048,059,027,069,140,101,142,020,088,055,117,123,158,159,026,024,057,150,085,104,058,054',
            'otherInfo.ProgramsCode' => 'required|in:ABM,CCTEC,CONEL,CUART,DIGAR,GAS,HOPER,HUMSS,MAWD,RESBO,STEM,TOPER,BSAT,84ASOM,ABCOMARTS,ASCT,ASOM,BSACT,BSBA,BSBM,BSCM,BSCOE,BSCS,BSED,BSEDCA,BSHRM,BSIT,BSITDA,BSN,BSOA,BSOM,BSREM,BSTM,BSTRM,CC3,CC4,CCEP,CCIT,CHS2,COA,DAIT,DCET,DHRM,DIT,DMA,DPN,HRA',
            'otherInfo.SchlYear' => 'required',
            'otherInfo.SchoolTerm' => 'required|in:1,2,4',
            'otherInfo.YrLevel' => 'required|in:S,T'
        ],
        PayBillsConfig::STLCW => [
            'account_number' => 'required|digits:12|numeric',
            'amount' => 'required|numeric|min:1|max:100000',
            'otherInfo.AccountName' => 'required|max:100',
            'otherInfo.BillNo' => 'required|digits:12|numeric'
        ],
        PayBillsConfig::STMWD => [
            'account_number' => 'required|digits_between:10,11',
            'amount' => 'required|numeric|min:1',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MI' => 'max:2',
            'otherInfo.LastName' => 'required|max:100'
        ],
        PayBillsConfig::SWSCO => [
            'account_number' => 'required|size:10',
            'amount' => 'required|numeric|min:1',
            'otherInfo.DueDate' => 'required|date_format:m/d/Y',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MI' => 'required|max:2',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.DisconnectionDate' => 'required|date_format:m/d/Y'
        ],
        PayBillsConfig::TRBNK => [
            'account_number' => 'required|size:11',
            'amount' => 'required|numeric|min:1|max:100000',
            'otherInfo.FirstName' => 'required|max:100',
            'otherInfo.MiddleName' => 'required|max:100',
            'otherInfo.LastName' => 'required|max:100',
            'otherInfo.TelephoneNo' => 'required|numeric|digits_between:7,11'
        ],
    ];



    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
  
        return true;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $billerCode = request()->route('biller_code');
        $billerCodeValidation = ['biller_code' => ['required', Rule::in(PayBillsConfig::billerCodes)]];

        if(in_array($billerCode, PayBillsConfig::billerCodes)) 
            return array_merge($this->validationArray[$billerCode], $billerCodeValidation);
        else return $billerCodeValidation;
    }


    protected function prepareForValidation()
    {
        $this->merge(['biller_code' => $this->route('biller_code')]);
    }   
}



