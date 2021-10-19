<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class CreateMerchantAccountRequest extends FormRequest
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
            // MERCHANT
            'name' => 'required',
            'type' => 'required',
            'house_no' => 'required',
            'city_municipality' => 'required',
            'province' => 'required',
            'authorized_representative' => 'required',
            'company_email' => 'required',
            'contact_number' => 'required',
            // USER ACCOUNT
            'email' => 'required',
            'mobile_number' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
        ];
    }
}
