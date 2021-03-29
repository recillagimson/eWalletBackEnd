<?php

namespace App\Http\Requests\Auth;

use App\Enums\UsernameTypes;
use App\Rules\IsPasswordValid;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'email' => 'required_without:'.UsernameTypes::MobileNumber.'|email',
            'mobile_number' => 'required_without:'.UsernameTypes::Email,
            'password' => [
                'required',
                'min:8',
                'max:16',
                'confirmed',
                new IsPasswordValid()
            ],
            'password_confirmation' => 'required|min:8|max:16',
        ];
    }
}
