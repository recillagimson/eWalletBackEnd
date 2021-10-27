<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MobileNumber;

class ValidatePinRequest extends FormRequest
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
            'mobile_number' => [
                'required_without:email',
                'max:11',
                new MobileNumber(),
            ],
            'email' => 'required_without:mobile_number|max:50|email',
            'pin_code' => 'required|digits:4|regex:/^(?!\b(.)\1+\b)/|confirmed',
            'pin_code_confirmation' => 'required',
        ];
    }
}
