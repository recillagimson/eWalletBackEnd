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



