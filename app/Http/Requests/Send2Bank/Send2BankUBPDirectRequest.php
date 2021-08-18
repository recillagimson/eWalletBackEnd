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
    public function authorize(): bool
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
        return [
            "recipient_account_no" => "required|max:12",
            "recipient_name" => "required",
            "remarks" => "required",
            "particulars" => "required",
            "amount" => 'required',
            'send_receipt_to' => 'required|email'
        ];
    }
}
