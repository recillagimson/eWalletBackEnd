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
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:5.00|max:100000.00'
        ],
        PayBillsConfig::PLDT6 => [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:200.00|max:100000.00',
        ],
        PayBillsConfig::ETRIP => [
            'account_number' => 'required|digits:12',
            'amount' => 'required|numeric|min:500.00|max:100000.00'
        ],
        PayBillsConfig::MWCOM => [
            'account_number' => 'required|digits:8',
            'amount' => 'required|numeric|min:20.00|max:100000.00'
        ],
        PayBillsConfig::SMART => [
            'account_number' => 'required|digits:10',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.Product' => 'required',
            'otherInfo.TelephoneNumber' => 'required|digits:10'
        ],
        PayBillsConfig::MWSIN => [
            'account_number' => 'required|digits:8',
            'amount' => 'required|numeric|min:20.00|max:100000.00'
        ],
        PayBillsConfig::CNVRG => [
            'account_number' => 'required|digits:13',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.AccountName' => 'required',
        ],
        PayBillsConfig::INEC1 => [

        ],
        PayBillsConfig::VIECO => [],
        PayBillsConfig::CGNAL => [],
        PayBillsConfig::MBCCC => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.ConsName' => 'required',
        ],
        PayBillsConfig::BNKRD => [],
        PayBillsConfig::BPI00 => [
            'account_number' => 'required|digits:16',
            'amount' => 'required|numeric|min:1.00|max:100000.00',
            'otherInfo.Service' => 'required',
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



