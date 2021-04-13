<?php

namespace App\Http\Requests\SendMoney;

use App\Rules\MobileNumber;
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
            'recipient_account_id ' => 'sometimes',
            'email' => 'required_without:mobile_number|email',
            'mobile_number' => [
                'required_without:email',
                new MobileNumber()
            ],
            'amount' => 'required|numeric|min:1',
            'message' => 'max:60|nullable'
        ];  
    }
}
