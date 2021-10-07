<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class MerchantVerifyRequest extends FormRequest
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
            "merchant_account_id" => "required",
            "currency_id" => "required",
            "is_allow_negative_balance" => "required",
            "spt_mdr" => "required",
            "mobile_number_1" => "required",
            "settlement_account" => "required",
            "credit_bank_percentage" => "required",
        ];
    }
}
