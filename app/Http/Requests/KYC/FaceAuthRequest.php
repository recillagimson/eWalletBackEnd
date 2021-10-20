<?php

namespace App\Http\Requests\KYC;

use Illuminate\Foundation\Http\FormRequest;

class FaceAuthRequest extends FormRequest
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
            'selfie' => 'mimes:jpeg,jpg,png,gif|required',
            'rsbsa_number' => 'required|exists:user_accounts,rsbsa_number'
        ];
    }
}
