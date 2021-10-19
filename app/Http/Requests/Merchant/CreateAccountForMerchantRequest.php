<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountForMerchantRequest extends FormRequest
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
            // USER ACCOUNT
            'email' => 'required',
            'mobile_number' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'merchant_account_id' => 'required|exists:merchant_accounts,id'
        ];
    }
}
