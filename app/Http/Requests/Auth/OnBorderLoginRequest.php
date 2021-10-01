<?php

namespace App\Http\Requests\Auth;

use App\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class OnBorderLoginRequest extends FormRequest
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
            'email' => 'required_without:mobile_number|email',
            'mobile_number' => [
                'required_without:email',
                new MobileNumber()
            ],
            'pin_code' => 'required|digits:4'
        ];
    }
}
