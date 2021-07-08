<?php

namespace App\Http\Requests\SecurityBank;

use Illuminate\Foundation\Http\FormRequest;

class PesoNetRequest extends FormRequest
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
            'amount' => 'required|min:1|numeric',
            'sender_address' => 'required',
            'beneficiary_address' => 'required',
            'account_number' => 'required',
            'bank_code' => 'required',
            'sender_first_name' => 'required',
            'sender_last_name' => 'required',
            'recipient_first_name' => 'required',
            'recipient_last_name' => 'required',
        ];
    }
}
