<?php

namespace App\Http\Requests\Auth;

use App\Enums\UsernameTypes;
use App\Rules\IsPasswordValid;
use App\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class ResetKeyRequest extends FormRequest
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
            'email' => 'required_without:'.UsernameTypes::MobileNumber.'|email',
            'mobile_number' => [
                'required_without:'.UsernameTypes::Email,
                new MobileNumber()
            ],
            'password' => [
                'required_without:pin_code',
                'min:' . config('auth.password_minlength'),
                'max:16',
                'confirmed',
                'different:email',
                new IsPasswordValid()
            ],
            'password_confirmation' => 'required_without:pin_code_confirmation|min:' . config('auth.password_minlength') . '|max:16',
            'pin_code' => [
                'required_without:password',
                'digits:4',
                'confirmed',
            ],
            'pin_code_confirmation' => 'required_without:password_confirmation|digits:4',
        ];
    }
}
