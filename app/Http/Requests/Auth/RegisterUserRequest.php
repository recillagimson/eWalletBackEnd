<?php

namespace App\Http\Requests\Auth;

use App\Rules\IsPasswordValid;
use App\Rules\MobileNumber;
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
            'mobile_number' => [
                'required_without:email',
                'max:20',
                new MobileNumber(),
                'unique:user_accounts,mobile_number'
            ],
            'email' => 'required_without:mobile_number|max:50|email|unique:user_accounts,email',
            'password' => [
                'required',
                'min:'.config('auth.password_minlength'),
                'max:16',
                'confirmed',
                'different:email',
                new IsPasswordValid()
            ],
            'password_confirmation' => 'required',
        ];
    }

}
