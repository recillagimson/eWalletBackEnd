<?php

namespace App\Http\Requests\FundTransfer;

use Illuminate\Foundation\Http\FormRequest;

class BankDetailsRequest extends FormRequest
{
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
    public function rules()
    {
        return [
            'account_name'   =>  'bail|required',
            'account_number' =>  'bail|required|numeric',
            'purpose'  =>   'bail|required',
            'email' =>  'bail|required|email',
            'amount'    =>  'bail|required|numeric',
            'transaction_remarks'   =>  'bail|required',
        ];
    }
}
