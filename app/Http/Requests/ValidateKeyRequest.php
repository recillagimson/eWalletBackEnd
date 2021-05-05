<?php

namespace App\Http\Requests;

use App\Rules\IsPasswordValid;
use Illuminate\Foundation\Http\FormRequest;

class ValidateKeyRequest extends FormRequest
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
            'current_password' => 'required_without:current_pin_code',
            'current_pin_code' => 'required_without:current_password',
            'new_password' => [
                'required_without:new_pin_code',
                'min:' . config('auth.password_minlength'),
                'max:20',
                'confirmed',
                'different:email',
                new IsPasswordValid()
            ],
            'new_password_confirmation' => 'required_without:new_pin_code_confirmation|min:' . config('auth.password_minlength') . '|max:16',
            'new_pin_code' => [
                'required_without:new_password',
                'digits:4',
                'confirmed',
            ],
            'new_pin_code_confirmation' => 'required_without:new_password_confirmation|digits:4',
        ];
    }
}
