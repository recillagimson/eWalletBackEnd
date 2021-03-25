<?php

namespace App\Http\Requests\Auth;

use App\Rules\IsPasswordValid;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'mobile_number' => 'required_without:email|max:20|unique:user_accounts,mobileNumber',
            'email' => 'required_without:mobile_number|max:50|email|unique:user_accounts,email',
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
