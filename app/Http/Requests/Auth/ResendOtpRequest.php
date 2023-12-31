<?php

namespace App\Http\Requests\Auth;

use App\Enums\OtpTypes;
use App\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResendOtpRequest extends FormRequest
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
            'email' => 'required_without:mobile_number|email',
            'mobile_number' => [
                'required_without:email',
                new MobileNumber()
            ],
            'otp_type' =>  [
                'required',
                Rule::in(OtpTypes::values)
            ]
        ];
    }
}
