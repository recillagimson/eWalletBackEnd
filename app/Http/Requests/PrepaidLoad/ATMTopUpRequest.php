<?php

namespace App\Http\Requests\PrepaidLoad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\NetworkTypes;

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
            'mobileNo' => array('required', 'regex:/^((639)|(09))[0-9]{9}$/'),
            'amount' => 'required|numeric|min:1',
            'provider' => array('required', Rule::in(
                    [
                        NetworkTypes::Globe, 
                        NetworkTypes::Smart, 
                        NetworkTypes::Sun, 
                        NetworkTypes::LoadCentral
                    ]
                )),
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
            'mobileNo.regex' => 'Please follow mobile number format starting with 09 or 639.',
        ];
    }
}
