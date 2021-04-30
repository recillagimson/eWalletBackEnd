<?php

namespace App\Http\Requests\Send2Bank;

use Illuminate\Foundation\Http\FormRequest;

class Send2BankUBPDirectRequest extends FormRequest
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
            "accountNo" => "required",
            "amount" => "required",
            "remarks" => "required",
            "particulars" => "required",
            "recipientName" => "required",
        ];
    }
}
