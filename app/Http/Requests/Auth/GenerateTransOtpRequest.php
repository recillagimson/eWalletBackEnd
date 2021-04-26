<?php

namespace App\Http\Requests\Auth;

use App\Enums\OtpTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateTransOtpRequest extends FormRequest
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
            'otp_type' => [
                'required',
                Rule::in(OtpTypes::transactionOtps)
            ]
        ];
    }
}
