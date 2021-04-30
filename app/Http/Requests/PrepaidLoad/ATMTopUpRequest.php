<?php

namespace App\Http\Requests\PrepaidLoad;

use Illuminate\Foundation\Http\FormRequest;

class ATMTopUpRequest extends FormRequest
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
            'productCode' => 'required',
            'mobileNo' => 'required|regex:/(09)[0-9]{9}/',
        ];
    }
}
