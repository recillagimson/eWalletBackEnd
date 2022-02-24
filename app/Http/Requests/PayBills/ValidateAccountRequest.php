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
        PayBillsConfig::CVMFICVMFI => [
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

        // STOP here

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
            'otherInfo.BankAccountNumber' => 'required_if:paymentMethod,CHECK|max:14',
            
        ],
        PayBillsConfig::GNTWC => [
            'account_number' => 'required|between:1,20',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.Name' => 'between:1,40|required',
            'otherInfo.ContactNo' => 'required|date:m/d/Y',
        ],
        PayBillsConfig::ILEC2 => [
            'account_number' => 'required|digits:9',
            'amount' => 'required|in:CASH',
            'otherInfo.Name' => 'max:100|required',
            'otherInfo.DueDate' => 'required|date:m/d/Y',
        ],
        PayBillsConfig::LARC1 => [
            'account_number' => 'required|between:8,9',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => 'max:100|required',
            'otherInfo.DueDate' => 'required|date:m/d/Y',
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
            'otherInfo.DueDate' => 'required|date:m/d/Y',
        ],
        PayBillsConfig::LOPCI => [
            'account_number' => 'required|digits:8',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.BankPaymentCode' => 'required|digits:9|numeric',
            'otherInfo.CheckDate' => 'date:m/d/Y|required_if:paymentMethod,CHECK',
            'otherInfo.ClientName' => 'max:100',
        ],
        PayBillsConfig::LPU01 => [
            'account_number' => 'required|digits:8',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.Campus' => 'required|in:Manila,Cavite',
            'otherInfo.StudentName' => 'required',
        ],
        PayBillsConfig::MAMEM => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.DueDate' => 'required|date:m/d/Y',
            'otherInfo.AccountName' => 'required|max:100',
        ],
        PayBillsConfig::MCLAC => [
            'account_number' => 'required|digits:10|numeric',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.DueDate' => 'required|date:m/d/Y',
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
            'otherInfo.DueDate' => 'required|date:m/d/Y',
            
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
            'otherInfo.DueDate' => 'required|date:m/d/Y',
        ],
        PayBillsConfig::SPLAN => [
            'account_number' => 'required|size:15',
            'amount' => 'required|numeric|min:1.00',
            'otherInfo.PlanType' => 'required|in:P,E',
            'otherInfo.AccountName' => 'required|max:100',
        ],
        // PayBillsConfig::SSS02 => [
        //     'account_number' => 'required|size:14',
        //     'amount' => 'required|numeric|min:1.00|max:100000',
        //     'otherInfo.PaymentType' => 'in:I,R',
        //     'otherInfo.LoanType' => 'in:SL,CL,EL,EDL,SIL,SLE',
        //     'otherInfo.PlatformType' => 'required|in:OTC,SS',
        //     'otherInfo.CountryCode' => 'required_if:otherInfo.PlatformType,SS|size:3',
        // ],
        PayBillsConfig::TWDIS => [
            'account_number' => 'required|digits:6|numeric',
            'amount' => 'required|numeric|min:1.00|max:100000',
            'otherInfo.Name' => 'required|max:100',
            'otherInfo.DueDate' => 'required|date:m/d/Y',
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



