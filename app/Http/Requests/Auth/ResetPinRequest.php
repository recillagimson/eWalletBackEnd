<?php

namespace App\Http\Requests\Auth;

use App\Enums\UsernameTypes;
use App\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class ResetPinRequest extends FormRequest
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
            'email' => 'required_without:' . UsernameTypes::MobileNumber . '|email',
            'mobile_number' => [
                'required_without:' . UsernameTypes::Email,
                new MobileNumber()
            ],
            'pin_code' => [
                'required',
                'digits:4',
                'confirmed',
            ],
            'pin_code_confirmation' => 'required|digits:4',
        ];
    }
}
