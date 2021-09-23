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
                'max:11',
                new MobileNumber(),
            ],
            'email' => 'required_without:mobile_number|max:50|email',
            'password' => [
                'required',
                'min:'.config('auth.password_minlength'),
                'max:20',
                'confirmed',
                'different:email',
                new IsPasswordValid()
            ],
            'password_confirmation' => 'required',
            // 'pin_code' => 'required|digits:4',
            // 'pin_code_confirmation' => 'required'
        ];
    }


}
