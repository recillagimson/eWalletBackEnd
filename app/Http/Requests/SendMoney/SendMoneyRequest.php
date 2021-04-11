<?php

namespace App\Http\Requests\SendMoney;

use Composer\DependencyResolver\Rule;
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
            'user_account_id' => 'sometimes',
            'email' => 'sometimes|required_without:mobile_number|email',
            'mobile_number' => 'sometimes|required_without:email',
            'amount' => 'required|numeric|min:1',
            'message' => 'max:60|nullable'
        ];  
    }
}
