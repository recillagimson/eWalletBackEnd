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
        PayBillsConfig::AEON1 => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
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
        ]
            

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



