<?php

namespace App\Http\Requests\SendMoney;

use Illuminate\Foundation\Http\FormRequest;

class SendMoneyRequest extends FormRequest
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
            'user_account_id' => 'required',
            'email' => 'required_without:mobile_number',
            'mobile_number' => 'required_without:email',
            'amount' => 'required|numeric|min:1',
            'message' => 'max:150'
        ];
    }
}
