<?php

namespace App\Http\Requests\PrepaidLoad;

use Illuminate\Foundation\Http\FormRequest;

class MobileNumberRequest extends FormRequest
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
            'mobileNo' => 'required|regex:/(639)[0-9]{9}/',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'mobileNo.regex' => 'Please follow mobile number format starting with Philippine Area Code(e.g. 639123456789).',
        ];
    }
}
