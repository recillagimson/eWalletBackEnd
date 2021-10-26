<?php

namespace App\Http\Requests\Send2Bank;

use Illuminate\Foundation\Http\FormRequest;

class FundTransferRequest extends FormRequest
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
            'bank_code' => 'required|max:50',
            'bank_name' => 'required|max:150',
            'account_number' => 'required|max:20',
            'account_name' => 'required|max:100',
            'recipient_first_name' => 'required_without:account_name|max:35',
            'recipient_last_name' => 'required_without:account_name|max:55',
            'amount' => 'required|numeric',
            'purpose' => 'required|max:50',
            'other_purpose' => 'required_if:purpose,Others|max:50',
            'send_receipt_to' => 'email'
        ];
    }
}
