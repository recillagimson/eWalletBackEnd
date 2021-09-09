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

        PayBillsConfig::MWCOM => [
            'account_number' => 'required|digits:8',
            'amount' => 'required|numeric|min:20.00|max:100000.00'
        ],
        PayBillsConfig::MECOR  => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:5.00|max:100000.00'
        ],
        PayBillsConfig::MWSIN => [
            'account_number' => 'required|digits:8',
            'amount' => 'required|numeric|min:20.00|max:100000.00'
        ],
        // Random Test accounts are still accepting, biller errror
         PayBillsConfig::RFID1 => [

        ],
        PayBillsConfig::ETRIP => [
            'account_number' => 'required|digits:12',
            'amount' => 'required|numeric|min:500.00|max:100000.00'
        ],
        PayBillsConfig::SMART => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.Product' => 'required',
            'otherInfo.TelephoneNumber' => 'required|digits:10'
        ],
        PayBillsConfig::SSS01 => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.PayorType' => 'required',
            'otherInfo.RelType' => 'required',
            'otherInfo.LoanAccountNo' => 'required|numeric',
            'otherInfo.LastName' => 'required',
            'otherInfo.FirstName' => 'required',
            'otherInfo.MI' => 'required',
            'otherInfo.PlatformType' => 'required'
        ],


        // 2nd BILLERS
        //  invalid JSON string error
         PayBillsConfig::SKY01 => [

        ],
        PayBillsConfig::MBCCC => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.ConsName' => 'required',
        ],
        PayBillsConfig::BPI00 => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.Service' => 'required',
        ],

        PayBillsConfig::CNVRG => [
            'account_number' => 'required|digits:13',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => 'required',
        ],
        PayBillsConfig::PLDT6 => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:200.00|max:100000.00',
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



